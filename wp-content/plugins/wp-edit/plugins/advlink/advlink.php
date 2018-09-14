<?php
/**
 *
 *
 * @author Josh Lobe
 * http://ultimatetinymcepro.com
 */
?>

<script type="text/javascript" src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="http://code.jquery.com/ui/jquery-ui-git.js"></script>
<script type="text/javascript" src="includes/advlink.js"></script>
<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/themes/smoothness/jquery-ui.css" />
<link rel="stylesheet" href="includes/advlink.css" />

<div id="body">
    	
    
    <div id="options_block">
    	
        <table cellpadding="3" id="adv_link_table">
        <tbody>
        <tr>
        	<td>
    		<label id="advlink_link_label" for="advlink_link" title="Enter a URL for this link.">Link Url</label>
            </td><td>
            <input type="text" id="advlink_link" placeholder="http://example.com" />
            </td>
        </tr>
        <tr>
        	<td>
    		<label id="advlink_title_label" for="advlink_title" title="Enter a title for this link.">Title</label>
            </td><td>
            <input type="text" id="advlink_title" />
            </td>
        </tr>
        <tr>
        	<td>
    		<label id="advlink_id_label" for="advlink_id" title="Enter an ID for this link.">ID</label>
            </td><td>
            <input type="text" id="advlink_id" />
            </td>
        </tr>
        <tr>
        	<td>
    		<label id="advlink_classes_label" for="advlink_classes" title="Enter space separated class names for this link.">Classes</label>
            </td><td>
            <input type="text" id="advlink_classes" />
            </td>
        </tr>
        <tr>
        	<td>
    		<label id="advlink_style_label" for="advlink_style" title="Enter custom css for this link.">Style</label>
            </td><td>
            <input type="text" id="advlink_style" />
            </td>
        </tr>
        <tr>
        	<td>
    		Target
            </td><td>
            <select id="advlink_target">
            	<option value="select">Select...</option>
            	<option value="_blank">_blank</option>
            	<option value="_self">_self</option>
            	<option value="_parent">_parent</option>
            	<option value="_top">_top</option>
            </select>
            </td>
        </tr>
        <tr>
        	<td>
    		NoFollow
            </td><td>
            <input type="checkbox" id="advlink_nofollow" /><label id="advlink_nofollow_label" for="advlink_nofollow">Off</label>
            </td>
        </tr>
        </tbody>
        </table>
        
    </div>
        
</div>
<br />
<button id="advlink_cancel" class="btn-default">Cancel</button> <button id="advlink_insert" class="btn-primary">Insert and Close</button>
<br /><br />