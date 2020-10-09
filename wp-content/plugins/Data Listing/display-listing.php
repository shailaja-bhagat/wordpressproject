<?php
    global $wpdb;
    $table_name = $wpdb->prefix.'data_listing';
    $dl_results = $wpdb->get_results("SELECT * FROM $table_name");
    // echo "<pre> result: "; print_r($dl_results);echo "</pre>";
?>
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <input type="text" id="searchInput" onkeyup="searchListing()" placeholder="Search here.." title="Type in a name">

            <table id="data_listing" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Age</th>
                    </tr>
                </thead>
                <?php
                    foreach( $dl_results as $dl_row){
                        $name   = $dl_row->name;
                        $email  = $dl_row->email;
                        $age    = $dl_row->age;
                    
                ?>
                <tbody>
                    <tr>
                        <td><?= $name ?></td>
                        <td><?= $email ?></td>
                        <td><?= $age ?></td>
                    </tr>
                </tbody>
                <?php } ?>
                <tfoot>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Age</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>