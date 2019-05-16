<div class="block">
<table class="table table-responsive">
  <thead>
	<tr>
		<th>First Name </th>
		<th>Last Name</th> 	
		<th>Email</th>
		<th>Status</th>
		<th>View</th>
		<th>Action</th>
	</tr>
  </thead>

  <tbody>
	{foreach $list as $row}
		<tr>
			{foreach from=$row key=i item=$item name=column}
				<td> {$item} </td>
			{if $smarty.foreach.column.index == 4}
			<td>
				<a href="https://galific.com/admin623dgtg6r/index.php?controller=AdminKbProductApprovalList&state=1&id={$item}&token={$smarty.get.token}">Accept </a>
				<a href="https://galific.com/admin623dgtg6r/index.php?controller=AdminKbProductApprovalList&state=2&id={$item}&token={$smarty.get.token}">Decline </a>
			</td>
			{/if}
			{/foreach}
		</tr>
 	{/foreach}
  </tbody>
</table>
  
   

<table class="form alternate free-disabled">
{if isset($img_url)} 
 <img src='{$img_url}'/>

{/if}

    {if isset($img_url2)} 
 <img src='{$img_url2}'/>

{/if}
</table>    
                           <br><br>                         </div>
<div id="kb_buy_link" style="text-align: center; padding: 25px; height: 140px;clear:both; background: #ffffff;">
    <div><span style="font-size:18px;">{l s='You are using the Free version of the module. Click here to buy the Paid version which is having the advanced features. ' mod='kbmarketplace'}</span>
        <br>
        <br>
        <a target="_blank" href="https://www.knowband.com/prestashop-marketplace"><span style="margin-left:30%;max-width:40% !important;font-size:18px;" class="btn btn-block btn-success action-btn">{l s='Buy Now' mod='kbmarketplace'}</span></a><div>
        </div>
    </div>
      
</div>
<script>
 
</script>    
