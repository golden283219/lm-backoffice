<?php

use common\services\ShowsWorker;

/**
 * @var $system_health object
 * @var $process_list array
 */
?>
<div class="row">

    <div class="col-sm-4">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="glyphicon glyphicon-flash"></i> CPU Usage</h3>
                <div class="progress">
                    <div class="progress-bar <?php echo $system_health->cpu_status->cpu_load > 80 ? 'progress-bar-danger' : 'progress-bar-info' ?>" role="progressbar" aria-valuenow="70"
                         aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $system_health->cpu_status->cpu_load; ?>%">
                        <span><?= $system_health->cpu_status->cpu_load ?>%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="glyphicon glyphicon-tasks"></i> RAM Usage</h3>
                <div class="progress">
                    <div class="progress-bar <?= calc_percent($system_health->ram_status->mem_total - $system_health->ram_status->mem_avail, $system_health->ram_status->mem_total) > 85 ? 'progress-bar-danger' : 'progress-bar-info'; ?>" role="progressbar" aria-valuenow="70"
                         aria-valuemin="0" aria-valuemax="100" style="width:<?= calc_percent($system_health->ram_status->mem_total - $system_health->ram_status->mem_avail, $system_health->ram_status->mem_total); ?>%">
                        <span><?= calc_percent($system_health->ram_status->mem_total - $system_health->ram_status->mem_avail, $system_health->ram_status->mem_total); ?>%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="glyphicon glyphicon-hdd"></i> HDD Usage</h3>
                <div class="progress">
                    <div class="progress-bar <?= calc_percent($system_health->hdd_status->disk_total_space - $system_health->hdd_status->disk_free_space, $system_health->hdd_status->disk_total_space) > 70 ? 'progress-bar-danger' : 'progress-bar-info' ?>" role="progressbar" aria-valuenow="70"
                         aria-valuemin="0" aria-valuemax="100" style="width:<?= calc_percent($system_health->hdd_status->disk_total_space - $system_health->hdd_status->disk_free_space, $system_health->hdd_status->disk_total_space); ?>%">
                        <span><?= calc_percent($system_health->hdd_status->disk_total_space - $system_health->hdd_status->disk_free_space, $system_health->hdd_status->disk_total_space); ?>%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <table class="table table-bordered">
            <thead class="thead-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col">original language</th>
                <th scope="col">Release Title</th>
                <th scope="col">Quality</th>
                <th scope="col">status</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($process_list as $process_item): ?>
                <tr>
                    <td><?= $process_item->id;?></td>
                    <td><?= $process_item->original_language;?></td>
                    <td><?= $process_item->release_title;?></td>
                    <td><?= isset($process_item->data->flag_qulity) ? $process_item->data->flag_qulity : '0'; ?></td>
                    <td><?= $process_item->status; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>