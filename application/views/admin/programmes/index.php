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
          <h1><?php echo  URI::segment(1) ?> <?php echo  __('programmes.' . URI::segment(2)) ?> Programmes</h1>
          <p style="margin-top:20px; margin-bottom:20px"><?php echo  __('programmes.' . URI::segment(2) . '_introduction', array('year' => URI::segment(1))) ?></p>
          <?php echo Messages::get_html()?>
          <div class="btn-group right" >
            <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
              Roll over all programmes to...
              <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
              <li><a>2014</a></li>
              <li><a>2015</a></li>
            </ul>
          </div>
          <div style="margin-top:20px; margin-bottom:20px">
            <a href="<?php echo action(URI::segment(1).'/'.URI::segment(2).'/programmes@create')?>" class="btn btn-primary">New Programme</a>
          </div>
          <?php if($programmes) : ?>
              <table class="table table-striped table-bordered table-condensed" width="100%">
              <thead>
                <tr>
                  <th><?php echo  __('programmes.title') ?></th>
                  <th>Subject</th>
                  <th><?php echo  __('programmes.excerpt') ?></th>
                  <th><?php echo  __('programmes.actions') ?></th>
                </tr>
              </thead><tbody>
              <?php foreach($programmes as $programme) : ?>
                <tr>
                  <td><?php echo $programme->title ?></td>
                  <td><?php //echo  $subjectList[$programme->subject_id] ?></td>

                  <td><?php echo  Str::limit(strip_tags($programme->summary), 40) ?></td>
                  <td><a class="btn btn-primary" href="<?php echo  action(URI::segment(1).'/'.URI::segment(2).'/programmes@edit', array($programme->id))?>"><?php echo  __('programmes.edit_programme') ?></a>

                    <a class="btn btn-primary" href="<?php echo  action(URI::segment(1).'/'.URI::segment(2).'/programmes@create', array($programme->id))?>"><?php echo  __('programmes.clone') ?></a>

                    <?php if($programme->live == 1): ?>
                      <a class="deactivate_toggler btn btn-danger" rel="<?php echo $programme->id ?>">Deactivate</a>
                    <?php else: ?>
                      <a class="activate_toggler btn btn-success" rel="<?php echo $programme->id ?>">Activate</a>
                    <?php endif; ?>

                  </td>
                </tr>
              <?php endforeach; ?>
              </tbody></table>
          <?php else : ?>
            <div class="well"><?php echo  __('programmes.no_programmes', array('level' => __('programmes.' . URI::segment(2)), 'year' => URI::segment(1))) ?></div>
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
        <p>Are you sure you want to delete this programme?</p>
      </div>
      <div class="modal-footer">
        <?php echo Form::open(URI::segment(1).'/'.URI::segment(2).'/programmes/deactivate', 'POST')?>
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
        <?php echo Form::open(URI::segment(1).'/'.URI::segment(2).'/programmes/activate', 'POST')?>
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
