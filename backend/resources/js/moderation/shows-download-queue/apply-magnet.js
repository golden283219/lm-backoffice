window.addEventListener('DOMContentLoaded', function () {
    function FileStructureItem(id, title, icon, droppable, draggable, fullname = '') {

        // Working Scope
        const ws = this;

        // Current Structure Item Id
        this.id = id;

        // Structure Item Name
        this.title = title;

        // Structure Type
        this.icon = icon;

        // Is Draggable Item
        this.draggable = draggable;

        // Is Droppable Item
        this.droppable = droppable;

        // Structure Children
        this.children = [];

        // File Full name
        this.fullname = fullname;

        // Structure Add Children
        this.addElement = function (element) {
            let addedElement = false;
            if (ws.children.length > 0) {
                ws.children.forEach(item => {
                    if (item.title === element.title) {
                        addedElement = item;
                    }
                });
            }

            if (!addedElement) {
                addedElement = element;
                ws.children.push(element);
            }

            return addedElement;
        };

        this.parseFile = function (path) {
            let folders = path.split('/');
            return {
                filename: folders.pop(),
                folders: folders
            };
        }
    }

    Vue.use(vueDraggableNestedTree);

    window.episodesTorrentUploader = new Vue({
        el: '#episodes-torrent-uploader',
        name: 'EpisodesTorrentUploader',
        props: [
            'id_tvshow',
            'csrfParam',
            'csrfToken'
        ],
        components: {
            'Tree': vueDraggableNestedTree,
            'TreeNode': vueDraggableNestedTree.TreeNode,
            'draggabletree': vueDraggableNestedTree.DraggableTree,
            'DraggableTreeNode': vueDraggableNestedTree.DraggableTreeNode
        },
        methods: {
            reconvertMultipleEpisodeMap: function () {
                const options = {
                    headers: {
                        'X-CSRF-Token': yii2app.csrf
                    }
                };

                axios.post('/moderation/episodes-download-queue/apply-torrent-bulk-with-map', {
                    episodesMap: this.getEpisodesMap(),
                    magnet: this.magnet,
                    id_tvshow: this.id_tvshow,
                    priority: this.priority
                }, options).then(function (response) {
                    if (response.data.success === true) {
                        const return_url = response.data.return_url;
                        const message = response.data.count + ' episode(s) successfully set to reconvert!';
                        window.location.href = '/site/redirect?type=success&url=' + return_url + '&message=' + message;
                    }
                }).catch(function (err) {
                    alert(err);
                });
            },
            getTorrentMetadata: function () {
                this.isFetchingTorrentData = true;
                axios.post(window.yii2app.WEBTORRENT_API + '/metadata/fetch', {
                    magnet: this.magnet
                }).then((response) => {
                    this.torrentFiles = response.data;
                    this.step++;
                    this.isFetchingTorrentData = false;
                });
            },
            getEpisodesMap: function () {
                let episodesMap = [];

                this.EpisodesTreeData.forEach(function (seasonItem) {
                    seasonItem.children.forEach(function (episodeItem) {
                        episodeItem.children.forEach(episodeMap => {
                            if (typeof(episodeMap.fullname) !== 'undefined') {
                                episodesMap.push({
                                    id_meta: episodeItem.id_meta,
                                    torrentPath: episodeMap.fullname,
                                    rel_title: episodeMap.title
                                });
                            }
                        });
                    });
                });

                return episodesMap;
            },
            detectFileType: function (name) {
                let defaultIcon = 'fa-file-o';

                const icons = [
                    {
                        extensions: ['mkv', 'avi', 'ts', 'mp4'],
                        icon: 'fa-file-video-o'
                    },
                    {
                        extensions: ['srt', 'vtt', 'txt'],
                        icon: 'fa-file-text'
                    },
                    {
                        extensions: ['png', 'jpg', 'jpeg'],
                        icon: 'fa-file-image-o'
                    }
                ];

                try {
                    const file_ext = name.split('.').pop();
                    if (typeof (file_ext) !== 'undefined') {
                        icons.forEach(iconSet => {
                            if (iconSet.extensions.includes(file_ext)) {
                                defaultIcon = iconSet.icon;
                            }
                        });
                    }
                } catch (e) {
                    console.error(e);
                }

                return defaultIcon;
            },
            humanStateValue: function (state) {
                const stateValues = [
                    'waiting(usenet)', 'on site', 'unused state', 'being converted', 'waiting(torrent)', 'missing'
                ];

                if (typeof (stateValues[state]) !== 'undefined') {
                    return stateValues[state];
                }

                return '(Not Set)';
            }
        },
        computed: {
            isDisabledStep1: function () {
                return !isValidaMagnetLink(this.magnet);
            },
            FilesTreeData: function () {
                const vm = this;

                let index = 0;
                const rootEl = new FileStructureItem(index, 'root', 'folder', false, false);

                this.torrentFiles.forEach(function (file) {
                    const file_info = rootEl.parseFile(file);

                    let elPtr = rootEl;
                    file_info.folders.forEach(function (folderName) {
                        elPtr = elPtr.addElement(new FileStructureItem(++index, folderName, 'folder', false, false));
                    });

                    elPtr.addElement(new FileStructureItem(++index, file_info.filename, vm.detectFileType(file_info.filename), false, true, file));
                });
                return rootEl.children;
            },
            EpisodesTreeData: function () {
                let formatted_data = [];
                for (let season_number in this.originalData) {
                    if (this.originalData.hasOwnProperty(season_number)) {
                        let episodes = [];
                        this.originalData[season_number].forEach(item => {
                            episodes.push({
                                title: 'Episode ' + item.episode,
                                air_date: item.air_date,
                                id_meta: item.id_meta,
                                draggable: false,
                                droppable: true,
                                state: item.state,
                                stateText: this.humanStateValue(item.state),
                                open: false
                            });
                        });

                        formatted_data.push({
                            title: 'Season ' + season_number,
                            icon: 'fa-folder',
                            draggable: false,
                            droppable: false,
                            children: episodes,
                            open: false
                        });
                    }
                }

                return formatted_data;
            }
        },
        data() {
            return {
                isUpdatingTorrentMap: false,
                isFetchingTorrentData: false,
                originalData: [],
                torrentFiles: [],
                step: 1,
                priority: 99,
                magnet: ''
            }
        },
        created() {
            axios.get(window.yii2app.apiBaseURL + '/v1/shows-download-queue/all-seasons-episodes?o=json&id_tvshow=' + window.id_tvshow).then((response) => {
                this.originalData = response.data;
            });
        }
    });
});
