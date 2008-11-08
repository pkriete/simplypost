<h2>Members</h2>

<p><?php echo anchor( backend_url('members/create'), 'Add a Member'); ?></p>

<table cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th>Username</th>
			<th>E-Mail</th>
			<th>User Group</th>
			<th>Join Date</th>
			<th>Post Count</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($members as $key => $member): ?>
		<tr<?php echo ($key & 1) ? ' class="switch"': ''; ?>>
			<td><?php echo $member->username; ?></td>
			<td><?php echo $member->email; ?></td>
			<td><?php echo ucfirst($member->user_group); ?></td>
			<td><?php echo date('m/j/Y', $member->join_date); ?></td>
			<td><?php echo $member->post_count; ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>