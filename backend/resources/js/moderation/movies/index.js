function MoviesModerationIndex () {
    const bulkActionsListSel = '#bulk-action-list';
    const applyBulkActionSel = '#apply-bulk-actions';

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

        runBulkAction('/moderation/movies/bulk-action', {action: action, ids: ids, _csrf: yii.getCsrfToken()}, function (response) {
            $(applyBulkActionSel).button('loading');

            if (response.success) {
                $.notify(response.message, 'success');
                $.pjax.reload('#grid-yt-converters-1-pjax', {timeout: false});
            } else {
                $.notify(response.message, 'error');
            }
        });
    });
}
