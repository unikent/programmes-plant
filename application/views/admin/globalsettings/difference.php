<?php echo View::make('admin.inc.meta')->render()?>
    <title>Courses Dashboard</title>
  </head>
  <body>
    <?php echo View::make('admin.inc.header')->render()?>
    <div class="container">

      <div class="row-fluid">

        <div class="span3"> <!-- Sidebar -->
          <div class="well">
            <?php echo View::make('admin.inc.sidebar')->render()?>
          </div>
        </div> <!-- /Sidebar -->

        <div class="span9 crud">
          <h1>Accept Changes</h2>
          <p>The following shows the differences between the two revisions.</p>
          <table class="table table-striped table-bordered">
            <thead>
              <th></th>
              <th>Current Version saved on <?php echo $subject->created_at; ?></th>
              <th>Revision created on <?php echo $revision->created_at ; ?></th>
            </thead>
            <tbody>
              <?php foreach ($old as $field => $value) : ?>
              <tr>
                <td><?php echo (!array_key_exists($field, $attributes)) ? __("globalsettings.$field") : $attributes[$field] ?></td>
                <td><?php echo  $value ?></td>
                <td>
                  <?php if (isset($diff[$field])) : ?>
                  <?php echo  $diff[$field] ?>
                  <?php else : ?>
                  <?php if (isset($new[$field])) { echo $new[$field]; } ?>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
          </table>
          <div class="form-actions">
            <a class="btn btn-danger promote_toggler" href="#promote_revision" rel="<?php echo  action(URI::segment(1).'/'.URI::segment(2).'/subjects.' . $subject->id . '@promote', array($revision->id))?>">Accept Changes And Promote To Live</a>
            <a class="btn btn-secondary" href="<?php echo url(URI::segment(1).'/'.URI::segment(2).'/subjects')?>">Ignore For Now</a>
          </div>
        </div><!-- span9 -->
      </div>
    </div> <!-- /container -->
    <div class="modal hide fade" id="promote_revision">
            <div class="modal-header">
              <a class="close" data-dismiss="modal">×</a>
              <h3>Are You Sure?</h3>
            </div>
            <div class="modal-body">
              <p>This will promote this revision to the live version of the subject for this year.</p>
              <p>Are you sure you want to do this?</p>
            </div>
            <div class="modal-footer">
              <?php echo Form::open('subjects/promote', 'POST')?>
                <a data-toggle="modal" href="#promote_revision" class="btn">Not Right Now</a>
                <a class="btn btn-danger" id="promote_now">Promote Revision</a>
            <?php echo Form::close()?>
            </div>
    <?php echo View::make('admin.inc.scripts')->render()?>

    <script>
      $('#promote_revision').modal({
        show:false
      }); // Start the modal

      // Populate the field with the right data for the modal when clicked
      $(".promote_toggler").click(function(){
        $('#promote_now').attr('href', $(this).attr('rel'));
        $('#promote_revision').modal('show');
      });
    </script>
  </body>
</html>
