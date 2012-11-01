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

        <div class="span9">
          <h1><?php echo  URI::segment(1) ?> <?php echo  __('subjects.' . URI::segment(2)) ?> Subjects</h1>
          <p style="margin-top:20px; margin-bottom:20px"><?php echo  __('subjects.' . URI::segment(2) . '_introduction', array('year' => URI::segment(1))) ?></p>
          <?php echo Messages::get_html()?>
          <div class="btn-group right" >
            <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
              Roll Over All Subjects To...
              <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
              <li><a>2014</a></li>
              <li><a>2015</a></li>
            </ul>
          </div>
          <div style="margin-top:20px; margin-bottom:20px">
            <a href="<?php echo action(URI::segment(1).'/'.URI::segment(2).'/subjects@create')?>" class="btn btn-primary">New Subject</a>
          </div>
          <?php if($subjects) : ?>
              <table class="table table-striped table-bordered table-condensed" width="100%">
              <thead>
                <tr>
                  <th><?php echo  __('subjects.title') ?></th>
                  <th><?php echo  __('subjects.excerpt') ?></th>
                  <th>Status</th>
                  <th><?php echo  __('subjects.actions') ?></th>
                </tr>
              </thead><tbody>
              <?php foreach($subjects as $subject) : ?>
                <tr>
                  <td><?php echo  $subject->title ?></td>
                  <td><?php echo  Str::limit(strip_tags($subject->summary), 40) ?></td>
                  <td><?php echo  ($subject->live==1) ? 'Live' : 'Inactive' ?></td>

                  <td>
                    <a class="btn btn-primary" href="<?php echo  action(URI::segment(1).'/'.URI::segment(2).'/subjects@edit', array($subject->id))?>"> <?php echo  __('subjects.edit_subject') ?></a>
                    <?php if($subject->live == 1): ?>
                      <a class="deactivate_toggler btn btn-danger" rel="<?php echo $subject->id ?>">Deactivate</a>
                    <?php else: ?>
                      <a class="activate_toggler btn btn-success" rel="<?php echo $subject->id ?>">Activate</a>
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


    <div class="modal hide fade" id="deactivate_subject">
      <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3>Are You Sure?</h3>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this subject?</p>
      </div>
      <div class="modal-footer">
        <?php echo Form::open(URI::segment(1).'/'.URI::segment(2).'/subjects/deactivate', 'POST')?>
        <a data-toggle="modal" href="#deactivate_subject" class="btn">Keep</a>
        <input type="hidden" name="id" id="postvalue" value="" />
        <input type="submit" class="btn btn-danger" value="Deactivate" />
        <?php echo Form::close()?>
      </div>
    </div>

    <div class="modal hide fade" id="activate_subject">
      <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3>Are You Sure?</h3>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to make the currently selected revision live?</p>
      </div>
      <div class="modal-footer">
        <?php echo Form::open(URI::segment(1).'/'.URI::segment(2).'/subjects/activate', 'POST')?>
        <a data-toggle="modal" href="#activate_subject" class="btn">Keep</a>
        <input type="hidden" name="id" id="postvalue2" value="" />
        <input type="submit" class="btn btn-danger" value="Activate" />
        <?php echo Form::close()?>
      </div>
    </div>


    <?php echo View::make('admin.inc.scripts')->render()?>
    <script>
      $('#deactivate_subject').modal({
        show:false
      }); // Start the modal

      // Populate the field with the right data for the modal when clicked
      $('.deactivate_toggler').each(function(index,elem) {
          $(elem).click(function(){
            $('#postvalue').attr('value',$(elem).attr('rel'));
            $('#deactivate_subject').modal('show');
          });
      });

      $('#activate_subject').modal({
        show:false
      }); // Start the modal

      // Populate the field with the right data for the modal when clicked
      $('.activate_toggler').each(function(index,elem) {
          $(elem).click(function(){
            $('#postvalue2').attr('value',$(elem).attr('rel'));
            $('#activate_subject').modal('show');
          });
      });
    </script>
  </body>
</html>
