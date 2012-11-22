<?php echo View::make('admin.inc.meta')->render(); ?>
    <title>Programme Plant - <?php echo __('fields.title', array('field_name' => __('fields.'.$field_type))); ?></title>
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
        
         
            
            
          <h1><?php echo __('fields.title', array('field_name' => __('fields.'.$field_type))); ?></h1>
          <p style="margin-top:20px; margin-bottom:20px"><?php echo  __('fields.introduction.'.$field_type); ?></p>
          <div style="margin-top:20px; margin-bottom:20px">
              <a href="<?php echo url('fields/'.$field_type.'/add')?>" class="btn btn-primary"><?php echo __('fields.btn.new'); ?></a>
          </div>
          
          <?php if ($sections): ?>
              <h2><?php echo  __('fields.table_sections_header_name') ?></h2>
              <table class="table table-striped table-bordered table-condensed">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody class="sortable-sections-tbody">
              <?php foreach($sections as $section): ?>
                <tr id="field-id-<?php echo $section->id?>">
                  <td><i class="icon-move"></i> <?php echo $section->name ?></td>
                  <td><a class="btn btn-primary" href="<?php echo action('/sections@edit', array($section->id)) ?>">Edit</a> <a class="delete_toggler btn btn-danger" rel="<?php echo $section->id ?>">Delete</a></td>
                </tr>
              <?php endforeach ?>
              </tbody></table>
           <?php endif ?>
           
           <h2><?php echo  __('fields.table_fields_header_name') ?></h2>
          <table class="table table-striped table-bordered table-condensed" width="100%">
              <thead>
                <tr>
                  <th><?php echo  __('fields.table_header_name') ?></th>
                  <th><?php echo  __('fields.table_header_type') ?></th>
                  <th></th>
                </tr>
              </thead>
              <tbody <?php echo $field_type == 'programmes' ? 'class="sortable-tbody"' : ''; ?>>
                <?php foreach($fields as $subject) : ?>
                <tr id="field-id-<?php echo $subject->id ?>">
                  <td><?php echo $field_type == 'programmes' ? '<i class="icon-move"></i> ' : ''; ?><?php echo $subject->field_name ?></td>
                  <td><?php echo $subject->field_type ?></td>
                  <td>

                    <a class="btn btn-primary" href="<?php echo url('fields/'.$field_type.'/edit/'.$subject->id);?>"><?php echo __('fields.btn.edit'); ?></a>

                    <?php if($subject->active == 1 ): ?>
                      <a class="btn btn-danger" href='<?php echo url('fields/'.$field_type.'/deactivate');?>?id=<?php echo $subject->id;?>'><?php echo __('fields.btn.deactivate'); ?></a>
                    <?php else: ?>
                      <a class="btn btn-success" href='<?php echo url('fields/'.$field_type.'/reactivate');?>?id=<?php echo $subject->id;?>'><?php echo __('fields.btn.reactivate'); ?></a>
                    <?php endif; ?>

                  </td>
                </tr>
              <?php endforeach; ?>
              </tbody>
          </table>
        </div>

      </div>

    </div> <!-- /container -->
<?php echo View::make('admin.inc.scripts')->render()?>
  </body>
</html>
