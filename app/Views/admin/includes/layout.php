<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Handglove Admin</title>

    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" href="<?php echo isset($favicon) ? $favicon : IMG_URL . 'favicon.ico'; ?>" />
    <link rel="apple-touch-icon" href="<?php echo isset($favicon) ? $favicon : IMG_URL . 'favicon.ico'; ?>" />

    <?php echo view('components/fonts'); ?>
    <?php 
    echo view(COMPILED_ASSETS_PATH . 'css/components/bootstrap');
    echo view(COMPILED_ASSETS_PATH . 'css/components/fontawesome');
    echo view(COMPILED_ASSETS_PATH . 'css/components/bootstrap-main');
    echo view(COMPILED_ASSETS_PATH . 'css/components/bootstrap-datepicker');
    echo view(COMPILED_ASSETS_PATH . 'css/admin/global');
    echo view(COMPILED_ASSETS_PATH . 'css/components/buttons');
    echo view(COMPILED_ASSETS_PATH . 'css/admin/navigation_bar');
    echo view (COMPILED_ASSETS_PATH . 'css/components/toastr');
    echo view(COMPILED_ASSETS_PATH . 'css/admin/left_panel');
    echo view(COMPILED_ASSETS_PATH . 'css/admin/main');
    ?>


    <?php 
    if(isset($styles) && !empty($styles) && is_array($styles)){
        foreach ($styles as $key => $style){ 
            if (is_string($style)){
                echo view($style); 
            }else{
                echo view($key, $style); 
            }
        }
    }
    ?>

    <script>
        csrfName = '<?php echo csrf_token() ?>';
        csrfCookie = '<?php echo config('cookie')->prefix . config('security')->cookieName ?>';
        baseUrl = "<?php echo base_url(); ?>";
        userId = "<?php echo session()->get('admin_id'); ?>";
    </script>
</head>

<body>
    <?php echo view('admin/includes/navigation_bar'); ?>
    <div class="wrapper" id="admin-wrapper">
        <div class="container">
            <div class="row">
                <div id="admin-sidebar" class="col-lg-3 col-md-12 col-sm-12 col-12">
                    <?php echo $this->include('admin/includes/sidebar') ?>
                </div>

                <div id="admin-main" class="col-lg-9 col-md-12 col-sm-12 col-12">
                    <?php echo $this->renderSection('content') ?>
                </div>
            </div>
        </div>

        <?php //echo $this->include('admin/includes/_footer') ?>

    </div>
    <!-- ./wrapper -->

    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous" ></script>
    <script src="<?= base_url('assets/dist/js/plugins/popper.min.js') ?>" ></script>
    <script src="<?= base_url('assets/dist/js/plugins/bootstrap-4.5.2/bootstrap.min.js') ?>" ></script>
    <script src="/assets/dist/js/plugins/bootstrap-datepicker.js" ></script>
    <script src="/assets/dist/js/components/global.min.js" ></script>
    <script src="/assets/dist/js/plugins/toastr.min.js" ></script>
    <script src="/assets/dist/js/components/navigation_bar.min.js" ></script>

    <?php 
    if(isset($scripts) && !empty($scripts) && is_array($scripts)){
        foreach ($scripts as $key => $script){
            if (is_array($script)){ 
                echo "<script src=\"{$key}\"";
                foreach ($script as $attribute => $value){
                    echo " {$attribute}=\"{$value}\" ";
                }
                echo " ></script>";
            }else{
                echo "<script src=\"{$script}\" ></script>";
            }
        }
    }
    ?>
    <?php echo $this->renderSection('customJS') ?>

</body>

</html>