<?php echo View::make('admin.inc.meta')->render()?>
<html>
  <head>
    <title>
      Courses Dashboard
    </title>
  </head>
  <body>
    <?php echo View::make('admin.inc.header')->render()?>
    <div class="container">
      <div class="row-fluid">
        <div class="span3">
          <!-- Sidebar -->
          <div class="well">
            <?php echo View::make('admin.inc.sidebar')->render()?>
          </div>
        </div><!-- /Sidebar -->
        <div class="span9 crud">
          <!-- does this need to be crud all the time? -->
          <?php echo $content ?>
        </div><!-- /span9 crud -->
      </div><!-- /row-fluid -->
    </div><!-- /container -->
    <?php echo View::make('admin.inc.scripts')->render()?>
  </body>
</html>
