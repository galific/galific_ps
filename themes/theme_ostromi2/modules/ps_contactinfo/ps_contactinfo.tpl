
<div class="block-contact-top">
	<div class="contact_cms">
		{if $contact_infos.email}
		<span class="cms1"><a href="mailto:{$contact_infos.email}">{$contact_infos.email}</a></span>
		{/if}
		{if $contact_infos.phone}
		<span class="cms2">{$contact_infos.phone}</span>
		{/if}
	</div>
</div>
