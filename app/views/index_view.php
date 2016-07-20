    <div id="page-title" class="page-title has-bg">
        <div class="bg-cover"><img src="assets/img/cover.jpg" alt="" /></div>
        <div class="container">
        </div>
    </div>
    <!-- end #page-title -->
    
    <!-- begin #content -->
    <div id="content" class="content">
        <!-- begin container -->
        <div class="container">
            <!-- begin row -->
            <div class="row row-space-30">
            <center>
                <h1><?=html_entity_decode($_SESSION['alias']->title)?></h1>
            </center>
                <p><?=html_entity_decode($_SESSION['alias']->text)?></p>

                <div
                  class="fb-like"
                  data-share="true"
                  data-width="450"
                  data-show-faces="true">
                </div>
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>