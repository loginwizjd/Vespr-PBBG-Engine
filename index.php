<?php
include('includes/header.php');
$page_title = "Home";
?>
<main class="container my-5">
    <!-- Content Blocks with Carousel Images -->
    <div class="my-5">
        <!-- First Block: Image Left, Text Right -->
        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <img src="uploads/main1.png" class="img-fluid rounded shadow-sm" alt="Image 1">
            </div>
            <div class="col-md-6">
                <h2>Welcome to Vespr.VIP</h2>
                <p>Your exclusive experience starts here. Join us and explore the best we have to offer.</p>
            </div>
        </div>

        <!-- Second Block: Image Right, Text Left -->
        <div class="row align-items-center mb-4">
            <div class="col-md-6 order-md-2">
                <img src="uploads/main2.png" class="img-fluid rounded shadow-sm" alt="Image 2">
            </div>
            <div class="col-md-6">
                <h2>Exclusive Features</h2>
                <p>Discover the exclusive features we provide to enhance your experience. Our offerings are designed with your needs in mind.</p>
            </div>
        </div>

        <!-- Third Block: Image Left, Text Right -->
        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <img src="uploads/main3.png" class="img-fluid rounded shadow-sm" alt="Image 3">
            </div>
            <div class="col-md-6">
                <h2>Join Us Today</h2>
                <p>Be part of our community and take advantage of the unique opportunities available. We look forward to welcoming you.</p>
            </div>
        </div>
    </div>
</main>
<?php include('includes/footer.php'); ?>
