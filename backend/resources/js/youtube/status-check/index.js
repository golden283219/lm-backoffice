function init_servers_status() {
    let items = document.querySelectorAll('[data-status-url]');
    items.forEach(async (item, index) => {
        if (typeof (item.dataset.statusUrl) !== 'undefined') {
            try {
                item.innerHTML = 'Loading..';
                let response = await axios.get(item.dataset.statusUrl);
                let badge = '<span class="badge">(Not Set)</span>';
                let lastUpdate = moment().subtract(response.data.last_update, 'seconds').fromNow();

                switch (response.data.status) {
                    case 'OK':
                        badge = '<span class="server--status--ok">OK</span>';
                        break;
                    case 'stalled':
                        badge = '<span class="server--status--error">Stuck</span>';
                        break;
                    case 'empty':
                        badge = '<span class="server--status--warning">Empty</span>';
                        break;
                }

                item.innerHTML =
                    `<div class="status">
            <div class="text-status">
              <span><b>Account:</b> ${response.data.yt_login}</span>
              <span><b>Last update:</b> ${lastUpdate}</span>
            </div>
            ${badge}
          </div>`;
            } catch (e) {
                item.innerHTML = '<span class="server--status--error" style="text-transform: uppercase;">Server Not Responding</span>';
            }
        }
    })
}

document.addEventListener('DOMContentLoaded', function () {
    let addServerModal = $('#add-converter-modal');
    const bulkActionsListSel = '#bulk-action-list';
    const applyBulkActionSel = '#apply-bulk-actions';
    init_servers_status();

    $(document).on('click', '#add-new-server', async function (e) {
        e.preventDefault();

        let contents = await axios.get('/youtube/servers/create');

        addServerModal.find('.modal-body').html(stripScriptTags(contents.data));
        addServerModal.find('.modal-header span').html('ADD NEW CONVERTER');

        addServerModal.modal('show');
    });

    $(document).on('click', '[data-modal-edit=1]', async function (e) {
        e.preventDefault();

        let contents = await axios.get(this.href);

        addServerModal.find('.modal-body').html(stripScriptTags(contents.data));
        addServerModal.find('.modal-header span').html('UPDATE CONVERTER');

        addServerModal.modal('show');
    });

    $(document).on('click', applyBulkActionSel, function () {
        let action = $(bulkActionsListSel).val(),
            ids = getCheckboxValues();
        if (action === '') {
            alert('Please Select Bulk Action');
            return;
        }

        if (ids.length === 0) {
            alert('Please Select Rows');
            return;
        }

        runBulkAction('/youtube/servers/' + action, {ids: ids, _csrf: yii.getCsrfToken()}, function (response) {
            $(applyBulkActionSel).button('loading');

            if (response.success) {
                $.notify(response.message, 'success');
                $.pjax.reload('#grid-yt-converters-1-pjax', {timeout: false});
            } else {
                $.notify(response.message, 'error');
            }
        });
    });

    $(document).on('pjax:success', init_servers_status);

    $(document).on('click', '#reload-server-status', init_servers_status);

    $(document).on('click', '[data-restart-conversion="1"]', async function (e) {
        e.preventDefault();
        let _this = this;
        try {
            await axios.get('http://' + _this.dataset.ip + '/process/action/restart');

            $.notify('Action:Restart Successfully executed on server: ' + _this.dataset.serverName, {
                className: 'success',
                arrowSize: 22,
                autoHideDelay: 12000
            });
        } catch (e) {
            $.notify('Error restarting converter: ' + _this.dataset.serverName)
        }
    });
});
