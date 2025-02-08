<?php
include("classes/cls_customer_master.php");
include("include/header.php");
?>
<body class="hold-transition skin-blue layout-top-nav">
<?php
include("include/body_open.php");
?>
<div class="wrapper">
<?php
include("include/navigation.php");
?>
<!-- Full Width Column -->
<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Customer Master
            </h1>
            <ol class="breadcrumb">
                <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Customer Master</li>
            </ol>
        </section>
        <!-- Main content -->
        <section class="content">
            <div style="margin-bottom:50px;">
                <button type="button" name="inputCreate" class="btn btn-info pull-right" onclick="location.href='frm_customer_master.php'">+ Add New</button>
            </div>
            <div class="box">
                <!-- /.box-header -->
                <div class="box-body">
                <?php
                    $_bll = new bll_customermaster(); // Change from district to customer
                    $_bll->pageSearch();
                ?>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.container -->
</div>
<!-- /.content-wrapper -->
<?php
include("include/footer.php");
?>
</div>
</body>
