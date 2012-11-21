<?php echo View::make('admin.inc.meta')->render()?>

<style>
.mainlist  {margin-left:0px;}
  .mainlist li {
    
    font-size:2em;
    list-style:none;
    
    
  }
  .mainlist li a {
     color:#000;
     background-color:#FBFBFB;
     display:block;
     padding:10px;
      border: 1px solid #DDD;
  }
  .mainlist li ul{
    padding:5px 0;
    margin:0 8px;
  }
  .mainlist li ul li {
    font-size:0.5em;
    padding:5px;
    background-color: transparent;
  }
  .mainlist li ul li a {
    padding:5px;
    background-color: transparent;
    border: hidden;



  }
  .btn-custom-width {

    width:180px;
    text-align:left;
  }

</style>

    <title>Courses Dashboard 2012</title>
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
<div>
<div class="page-header">
  <h1>Change Requests</h1>
</div>
</div>
<?php if ($revisions) : ?>
<table class="table table-striped">
  <thead>
    <tr>
      <th></th>
      <th>Title</th>
      <th>Year</th>
      <th>Author</th>
      <th>Submitted</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($revisions as $revision) : ?>
    <tr>
      <td><input type="checkbox"></td>
      <td><?php echo  $revision->title ?></td>
      <td><?php echo  $revision->year ?></td>
      <td><?php echo  $revision->created_by ?></td>
      <td><?php echo  Date::forge($revision->created_at)->ago() ?></td>
      <td>
        <a class="btn btn-info" href="<?php echo  action("/$revision->year/ug/subjects/$revision->subject_id/difference/$revision->id") ?>">See Differences</a>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
        <?php else : ?>
        No changes right now.
        <?php endif; ?>
        </div>

      </div>
      <div class="row-fluid">
        <div class="span12">
          
        </div>
      </div>
    </div> <!-- /container -->

    <?php echo View::make('admin.inc.scripts')->render()?>
  </body>
</html>
