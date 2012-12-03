<?php echo View::make('admin.inc.meta')->render()?>
    <title>
      Programmes Plant
    </title>
  </head>
  <body>
    <?php echo View::make('admin.inc.header')->render()?>
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span3">
          <!-- Sidebar -->
          
            <?php echo View::make('admin.inc.sidebar')->render()?>
          
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
