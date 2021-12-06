window.addEventListener('DOMContentLoaded', function () {
    const addServerModalSel = '#add-server-modal';
    const addServerSel = '#add-streaming-server';
    const bulkActionsListSel = '#bulk-action-list';
    const applyBulkActionSel = '#apply-bulk-actions';

    $(document).on('click', addServerSel, function () {
        $(addServerModalSel).modal('show');
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

        runBulkAction('/system/fe-servers/' + action, {ids: ids}, function (response) {
            $(applyBulkActionSel).button('loading');
            if (response.success) {
                $.notify(response.message, 'success');
                $.pjax.reload('#grid-fe-servers-1-pjax', {timeout: false});
            } else {
                $.notify(response.message, 'error');
            }
        });
    });
})