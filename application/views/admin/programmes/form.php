<h1><?php echo ( $create ? __('programmes.create_programme_title') : $programme->$title_field ); ?><?php echo isset($programme->award->name) ? ' - <em>'.$programme->award->name.'</em>' : '' ; ?></h1>
<p><?php echo ( $create ? __('programmes.create_introduction') : __('programmes.edit_introduction') ); ?></p>

<?php echo Messages::get_html()?>

<?php echo Form::open_for_files(URI::segment(1).'/'.URI::segment(2).'/programmes/'.( $create ? 'create' : 'edit' ), 'POST', array('class'=>'form-horizontal'));?>


<?php if (! $create): ?>
  <?php echo  Form::hidden('programme_id', $programme->id); ?>
<?php endif; ?>

<div class="control-group">
  <?php echo Form::label('year', "Year",array('class'=>'control-label'))?>
  <div class="controls">
      <span class="input-xlarge uneditable-input"><?php echo  ( $create ? URI::segment(1) : $programme->year )?></span>
      <?php echo  Form::hidden('year', ( $create ? URI::segment(1) : $programme->year ), array('class'=>'uneditable-input'))?>
  </div>
</div>

<?php echo View::make('admin.inc.partials.formfields', array('year' => $year,'sections' => $sections, 'programme' => isset($programme) ? $programme : null,'create'=>($create && !$clone), 'from' => 'programmes'))->render(); ?>


<?php echo Form::actions('programmes')?>

<?php if (isset($revisions)) : ?>
  <?php echo View::make('admin.programmes.partials.revisions', array('revisions' => $revisions, 'subject' => $programme, 'title_field' => $title_field))->render(); ?>
<?php endif; ?>





