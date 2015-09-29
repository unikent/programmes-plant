<h1>Inbox</h1>
<p>These are the incoming revisions of programmes for you to review.</p>
<?php echo Messages::get_html()?>

<?php if ($for_review) : ?>
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>Programme title</th>
            <th>Programme type</th>
            <th>Year</th>
            <th>Submitted by</th>
            <th>Updated</th>
            <th>Editor tools</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($for_review as $revision) : ?>
            <?php
            $revision_class = get_class($revision);
            $model = $revision_class::$programme_model;
            $title_field = $model::get_title_field();
            $note = $revision->note();
            ?>
            <tr>
                <td><?php echo $revision->$title_field ?><?php echo !empty($note)?'<br/><em class="admin-note">'.$note->short_note . '</em>':''; ?></td>
                <td><?php echo strstr($model, 'UG') ? "Undergraduate" : "Postgraduate"; ?></td>
                <td><?php echo $revision->year ?></td>
                <td>
                    <?php $user_details = User::where('username', '=', $revision->edits_by)->first(array('email', 'fullname'));
                    if (is_null($user_details)) {
                        echo $revision->edits_by;
                    } else {
                        echo $user_details->fullname . ' (' .  HTML::mailto(Str::lower($user_details->email)) . ')';
                    }
                    $type_for_url = strstr($model, 'UG') ? 'ug' : 'pg';
                    $view_link = action( $revision->year . '/' . $type_for_url . '/programmes/' . $revision->programme_id . '@view_revision', array($revision->id));$diff_link = action( $revision->year . '/' . $type_for_url . '/programmes/' . $revision->programme_id . '@review', array($revision->id));
                    ?>
                </td>
                <td><?php echo Date::forge($revision->updated_at)->ago(); ?></td>
                <td>
                    <a class="btn btn-primary" href="<?php echo $diff_link; ?>">Review</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php else : ?>
<div class="well">
  <p>Nothing for you to review or edit right now - why not treat yourself?</p>
</div>
<?php endif; ?>