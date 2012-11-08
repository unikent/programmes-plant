<?php echo View::make('admin.inc.meta')->render();

$short_url = 'fields/'.$field_type;

?>
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

        <div class="span9">
          <h1><?php echo $field_type ?> field configuration</h1>
          <p style="margin-top:20px; margin-bottom:20px"><?php echo  __('fields.' . URI::segment(2) . '_introduction', array('year' => URI::segment(1))) ?></p>
          Please take care when editing these options as they can have a signifcant impact on the functionalty of the programmes plant.
          <div style="margin-top:20px; margin-bottom:20px">
            <a href="<?php echo url($short_url.'/add')?>" class="btn btn-primary">New Field</a>
          </div>
          <?php if(true) : ?>
              <table class="table table-striped table-bordered table-condensed" width="100%">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Type</th>
                  <th>View</th>
                  <th><?php echo  __('subjects.actions') ?></th>
                </tr>
              </thead><tbody>
              <?php foreach($fields as $subject) : ?>
                <tr>
                  <td><?php echo  $subject->field_name ?></td>
                  <td><?php echo  $subject->field_type ?></td>
                   <td>N/A</td>
                  <td>

                    <a class="btn btn-primary" href="<?php echo url($short_url.'/edit/'.$subject->id);?>">Change</a> 

                    <?php if($subject->active == 1 ): ?>
                      <a class="btn btn-danger" href='<?php echo url($short_url.'/deactivate');?>?id=<?php echo $subject->id;?>'>Deactivate</a>
                    <?php else: ?>
                      <a class="btn btn-success" href='<?php echo url($short_url.'/reactivate');?>?id=<?php echo $subject->id;?>'>Reactivate</a>
                    <?php endif; ?>

                  </td>
                </tr>
              <?php endforeach; ?>
              </tbody></table>
          <?php else : ?>
            <div class="well"><?php echo  __('subjects.no_subjects', array('level' => __('subjects.' . URI::segment(2)), 'year' => URI::segment(1))) ?></div>
          <?php endif; ?>
        </div>

      </div>

    </div> <!-- /container -->
    <div class="modal hide fade" id="delete_subject">
      <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3>Are You Sure?</h3>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this subject?</p>
      </div>
      <div class="modal-footer">
        <?php echo Form::open(URI::segment(1).'/'.URI::segment(2).'/subjects/delete', 'POST')?>
        <a data-toggle="modal" href="#delete_subject" class="btn">Keep</a>
        <input type="hidden" name="id" id="postvalue" value="" />
        <input type="submit" class="btn btn-danger" value="Delete" />
        <?php echo Form::close()?>
      </div>
    </div>
    <?php echo View::make('admin.inc.scripts')->render()?>
    <script>
      $('#delete_subject').modal({
        show:false
      }); // Start the modal

      // Populate the field with the right data for the modal when clicked
      $('.delete_toggler').each(function(index,elem) {
          $(elem).click(function(){
            $('#postvalue').attr('value',$(elem).attr('rel'));
            $('#delete_subject').modal('show');
          });
      });
    </script>
  </body>
</html>
