<?php echo View::make('admin.inc.meta')->render()?>
    <title>Programmes Plant</title>
  </head>
  <body>
    <?php echo View::make('admin.inc.header')->render()?>
    <div class="container">

      <div class="row-fluid">

        <div class="span24 crud">
          <h1><?php echo $subject->title ?></h1>
          <p><?php echo $subject->summary ?></p>

          <div class="form-actions">
            <a class="btn" href="<?php echo url(URI::segment(1).'/'.URI::segment(2).'/subjects')?>">Go Back</a>
            <a class="btn btn-primary" href="<?php echo action(URI::segment(1).'/'.URI::segment(2).'/subjects@edit', array($subject->id))?>">Edit</a>
          </div>
        </div>

      </div>

    </div> <!-- /container -->

    
  </body>
</html>
