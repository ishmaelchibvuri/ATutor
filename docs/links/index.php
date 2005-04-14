<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
define('AT_INCLUDE_PATH', '../include/');

require (AT_INCLUDE_PATH.'vitals.inc.php');

if (isset($_GET['view'])) {
	$_GET['view'] = intval($_GET['view']);
	//add to the num hits
	$sql = "SELECT Url, hits FROM ".TABLE_PREFIX."resource_links WHERE LinkID=$_GET[view]";
	$results = mysql_query($sql,$db);

	if ($row = mysql_fetch_assoc($results)) { 
		if (!authenticate(AT_PRIV_LINKS, AT_PRIV_RETURN)) {

			$row['hits']++;
			$sql = "UPDATE ".TABLE_PREFIX."resource_links SET hits=$row[hits] WHERE LinkID=$_GET[view]";
			mysql_query($sql,$db);
		}

		//redirect
		header('Location: ' . $row['Url']);
		exit;
	}
}

require (AT_INCLUDE_PATH.'lib/links.inc.php');
require (AT_INCLUDE_PATH.'header.inc.php');

if ($_GET['reset_filter']) {
	unset($_GET);
}

$_GET['cat_parent_id'] = intval($_GET['cat_parent_id']);
$categories = get_link_categories();

if ($_GET['search']) {
	$page_string .= SEP.'search='.urlencode($_GET['search']);
	$search = $addslashes($_GET['search']);
	$search = str_replace(array('%','_'), array('\%', '\_'), $search);
	$search = '%'.$search.'%';
	$search = "((LinkName LIKE '$search') OR (description LIKE '$search'))";
} else {
	$search = '1';
}

if ($_GET['cat_parent_id']) {
    $children = get_child_categories ($_GET['cat_parent_id'], $categories);
    $cat_sql = "C.CatID IN ($children $_GET[cat_parent_id])";
	$parent_id = intval($_GET['cat_parent_id']);
} else {
    $cat_sql = '1';   
    $parent_id = 0;	
}

$sql = "SELECT * FROM ".TABLE_PREFIX."resource_links L INNER JOIN ".TABLE_PREFIX."resource_categories C USING (CatID) WHERE C.course_id=$_SESSION[course_id] AND L.Approved=1 AND $search AND $cat_sql";

$result = mysql_query($sql, $db);
$num_results = mysql_num_rows($result);

?>
<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<div class="input-form">
        <div class="row">
			<h3><?php echo _AT('results_found', $num_results); ?></h3>
		</div>

        <div class="row">
			<label for="cat_parent_id"><?php echo _AT('select_cat'); ?></label>
	        <br />
			<?php if (!empty($categories)): ?>
				<select name="cat_parent_id" id="category_parent"><?php
						if ($parent_id) {
							$current_cat_id = $parent_id;
							$exclude = false; /* don't exclude the children */
						} else {
							$current_cat_id = $cat_id;
							$exclude = true; /* exclude the children */
						}

						echo '<option value="0">&nbsp;&nbsp;&nbsp; '._AT('cats_all').' &nbsp;&nbsp;&nbsp;</option>';
						select_link_categories($categories, 0, $current_cat_id, FALSE);
					?>
				</select>
			<?php endif; ?>
		</div>

		<div class="row">
			<label for="search"><?php echo _AT('search'); ?> (<?php echo _AT('title').', '._AT('description'); ?>)</label><br />
			<input type="text" name="search" id="search" size="20" value="<?php echo htmlspecialchars($_GET['search']); ?>" />
		</div>


		<div class="row buttons">
			<input type="submit" name="filter" value="<?php echo _AT('filter'); ?>" />
			<input type="submit" name="reset_filter" value="<?php echo _AT('reset_filter'); ?>" />
		</div>
</div>
</form>

<?php if ($row = mysql_fetch_assoc($result)) : ?>
	<table class="data static" summary="" rules="cols">
	<thead>
	<tr>
		<th scope="col"><?php echo _AT('title');       ?></th>
		<th scope="col"><?php echo _AT('category');    ?></th>
		<th scope="col"><?php echo _AT('description'); ?></th>
	</tr>
	</thead>
	<tbody>
		<?php
		do {
			?>
			<tr onmousedown="document.form['m<?php echo $row['LinkID']; ?>'].checked = true;">
				<td><a href="links/index.php?view=<?php echo $row['LinkID']; ?>" target="_new" title="<?php echo AT_print($row['LinkName'], 'resource_links.LinkName'); ?>"><?php echo AT_print($row['LinkName'], 'resource_links.LinkName'); ?></a></td>
				<td><?php echo AT_print($row['CatName'], 'resource_links.CatName'); ?></td>
				<td><?php echo AT_print($row['Description'], 'resource_links.Description'); ?></td>
			</tr>
	<?php 
		} while ($row = mysql_fetch_assoc($result)); ?>
	</tbody>
	</table>
<?php else: ?>
	<p><?php echo _AT('no_links'); ?></p>
<?php endif; ?>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>