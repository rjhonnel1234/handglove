<?php 
/**
 * FOR DOCUMENTATION PURPOSE ONLY.
 * 
 * This file requires input variables:
 * 
 * echo view('components/header', array(...example below));
 * 
 * array(
 *  'title' => 'Main Title',
 *  'description' => 'Main Description',
 *  'url' => 'Current Link Here',
 *  'keywords' => 'Keywords Details',
 *  'meta' => array(
 *    'title' => 'Custom Meta Title',
 *    'description' => 'Custom Meta Description',
 *    'image' => 'path/to/thumb/image'
 *  ),
 *  'styles' => 'Consolidated Content of CSS (For pagespeed we directly embed the CSS)',
 *  'favicon' (OPTIONAL) => 'path/to/icon'
 * ); 
 */
?>
<!DOCTYPE html>
<html lang="en">
    <head>
    <!-- PAGE MAIN DETAILS -->
    <title><?php echo $title; ?></title>
    <meta name="robots" content="noindex"> 
    
    <meta name="description" content="<?php echo $description; ?>" />
    <meta name="keywords" content="<?php echo $keywords; ?>" />
    <link rel="canonical" href="<?php echo $url; ?>" />

    <!-- STANDARD HTML PAGE CONFIGURATION -->
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" href="<?php echo isset($favicon) ? $favicon : IMG_URL . 'favicon.ico'; ?>" />
    <link rel="apple-touch-icon" href="<?php echo isset($favicon) ? $favicon : IMG_URL . 'favicon.ico'; ?>" />

    <!-- OPEN-GRAPH META DETAILS -->
    <meta name="og:type" content="website" />
    <meta name="og:url" content="<?php echo $url; ?>" />
    <meta name="og:title" content="<?php echo $meta['title']; ?>" />
    <meta name="og:image" content="<?php echo $meta['image']; ?>" />
    <meta name="og:description" content="<?php echo $meta['description']; ?>" />

    <!-- TWITTER META DETAILS -->
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:title" content="<?php echo $meta['title']; ?>" />
    <meta name="twitter:description" content="<?php echo $meta['description']; ?>" />
    <meta name="twitter:image" content="<?php echo $meta['image']; ?>" />

    <!-- APPLICATION JSON LD -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "website",
        "url": "<?php echo $url; ?>",
        "name": "<?php echo $title; ?>",
        "description": "<?php echo $description; ?>"
        "image": "<?php echo $meta['image']; ?>"
    }
    </script>
    <?php echo view('components/fonts'); ?>
    
    <?php 
        foreach ($styles as $key => $style){ 
            if (is_string($style)){ 
                echo view($style);
            }else{ 
                echo view($key, $style);
            } 
        } 
    ?>

    <?php echo view('__compiled_assets__/css/components/notifications.php'); ?>
</head>
<body data-scroll-animation="true">
<div class="tp-home">
    <?php echo view('components/navigation_bar_v3'); ?>
