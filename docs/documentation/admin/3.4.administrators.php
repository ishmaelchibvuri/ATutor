<?php require('./body_header.inc.php'); ?>

<h2>3.4 Administrators</h2>
	<p>An ATutor installation can be maintained by multiple administrators each with their own privilege access level. The three kinds of administrator accounts are described below.</p>

	<dl>
		<dt>Super Administrator</dt>
		<dd>This administrator has no restrictions and has access to all of the administrator options. This is the only administrator type that can create and delete other administrator accounts. There must always be atleast one Super Administrator account.</dd>

		<dt>Active Administrator</dt>
		<dd>An administrator account whose access is limited. This administrator only has privileged access to sections that they were assigned to when their account was created by the Super Administrator.</dd>

		<dt>Inactive Administrator</dt>
		<dd>An addministrator account who has not been assigned any access privileges. As a result, this administrator cannot sign-in.</dd>
	</dl>

<?php require('./body_footer.inc.php'); ?>