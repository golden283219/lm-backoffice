$(document).ready(function() {
    // Get Url Param
    var url_string = window.location.href;
    var url = new URL(url_string);
    var id = url.searchParams.get("id");

    // Init Select
    $('.select-box').select2({
        placeholder: "Search Movie",
        minimumInputLength: 3,
        multiple: false,
        ajax: {
            url: '/moderation/movies-featured/search',
            dataType: 'json',
            data: function (params) {
              var query = {
                search: params.term,
              }

              // Query parameters will be ?search=[term]&type=public
              return query;
            },
            processResults: function (data) {
              let results = [];
              results.push({
                id: '',
                text: '[Any Movie]'
              });
                console.log(results);
              data.forEach(item => {
                results.push({
                  id: item.id_movie,
                  text: item.title + ' (' + item.year + ')'
                });
              });
              console.log(results);
              return {
                results: results
              };
            }
          }
    });

    // If id exist
    if(typeof (id) !== 'undefined'){
        $.ajax({
            url: "/moderation/movies-featured/search-by-id?id=" + id, success: function (result) {
                $(".select-box").data('select2').trigger('select', {
                    data: {
                        "id": result.id_movie,
                        "text": result.title + ' (' + result.year + ')'
                    }
                });
            }
        });
    }
});

