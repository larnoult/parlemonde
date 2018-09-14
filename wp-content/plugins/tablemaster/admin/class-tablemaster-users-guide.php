<?php

if (!class_exists('TableMaster_Users_Guide') )  {
class TableMaster_Users_Guide {
	public function __construct() {}
	
	public static function print_users_guide()  {

		if ( is_admin() ) { ?> 
			<h1 style="padding-top:40px;"><?php echo esc_html( get_admin_page_title() ); ?></h1>
 			<div class="wrap">
				<div id="wpcontent-body">
		<?php  } ?> 
<style>
#tablemaster-users-guide .tm-title {margin-top: 30px;font-weight:bold;color:blue;}
#tablemaster-users-guide .tm-section-description {}
#tablemaster-users-guide .tm-anchor {margin-bottom:1.25rem;}
#tablemaster-users-guide .tm-keyword {}
#tablemaster-users-guide .tm-example-table, #tablemaster-users-guide .tm-keyword-description, #tablemaster-users-guide .tm-default-value {padding-left:40px;}
#tablemaster-users-guide .tm-shortcode-example {padding-left: 60px;color: red;}
#tablemaster-users-guide .tm-html-entities {margin-left:auto; margin-right:auto; text-align:center;}
#tablemaster-users-guide td {padding-left: 10px!important;}
#tablemaster-users-guide .tm-tips  {list-style: disc;padding-left: 2.0em;margin-left: 2.0em;}
</style>
			<div id="tablemaster-users-guide">
				<?php if (!is_admin() ) { ?>
					<h1>TableMaster User's Guide</h1> 
				<?php  } else { ?> 
				<!--	<div style="padding-top:40px;" /> -->
				<?php } ?>
				
				<h2 id="overview" class="tm-title">Overview</h2>
				
 				<p class="tm-section-description">Welcome to the TableMaster User's Guide. This guide describes how to use TableMaster and provides working examples using each keyword that you can tryout in your page or post. TableMaster was created because I needed to print a list of members contact information for a non-profit organization I was working for.  The membership data was in a table in my Wordpress database, but there were no Wordpress plugins available that would print data directly from the Wordpress database.  Therefore, TableMastert was built to solve that problem. TableMaster provides several options you can use to identify and extract the data you want to print from your database, and several options you can use to format the data into a nicely styled table on your page or post. All of these options are specified using keywords with the 'tablemaster' shortcode, and several of the keywords (which are denoted with the <?php if( is_admin() ) { ?> <span class="dashicons dashicons-admin-settings" style="font-size:120%;color:red;"></span> settings <?php } else { ?><i class="color_red fa fa-gear"></i> gear <?php } ?> icon) can be set to a user-define default value from the General Options screen in the admin area by selecting the 'TableMaster' option from the Settings menu.</p>
				<p>There are three basic steps to printing a table with TableMaster:</p>

				<ul class="tm-tips">
					<li>Step 1: <a href="#step_1">Specify the data to print</a></li>
					<li>Step 2: <a href="#step_2">Add your own table styling</a> (optional)</li>
					<li>Step 3: <a href="#step_3">Add jQuery Datatables and Buttons</a> (optional)</li>
				</ul>

				<p>And, don't miss these extras at the end of the User's Guide:</p>

				<ul class="tm-tips">
					<li><a href="#advanced_keywords">TableMaster Advanced Keywords</a></li>
					<?php if ( !is_admin() ) { ?> <li><a href="#example_styles">Example Table Styles Provided with TableMaster</a></li> <?php  } ?>
					<li><a href="#trouble_shooting">Troubleshooting Tips</a></li>
				</ul>
				<h2 id="step_1" class="tm-title">Step 1: Specify the data to print</h2>

				<p class="tm-section-description">TableMaster is designed to extract data from a database table, and so it naturally needs to be able to accept and process a MySQL command. However, no knowledge of MySQL commands is necessary to use TableMaster. In it's simplest form, the only keyword you must provide with the tablemaster shortcode is the name of the database table (or view) that you want to print. But, you also have the option to provide a simple, or a very complex MySQL command to extract the data you want to print. This section describles the keywords you can use to extract the data from the database table.</p>

				<h4 id="table_keyword"class="tm-keyword">table Keyword</h4>
					<p class="tm-default-value">Default Value: None</p>
					<p class="tm-keyword-description">The <em><b>table</b></em> keyword is one of the keywords you can use to specify the data you would like to print by simply providing the name of the database table you want to print. This example will print all rows and all columns in the tm_example_table table. <em>Note: the tm_example_table has 5 columns (Horse, Breed, Score, Test, Rider) and 7 rows.</em></p>
					<!-- Shortcode -->
						<?php $shortcode = '[tablemaster table="tm_example_table"]'; ?>
						<p class="tm-shortcode-example">Example: <?php echo $shortcode ?> </p>
						<?php if ( !is_admin() ) { ?> <div class="tm-example-table"> <?php echo do_shortcode( $shortcode ); ?>  </div> <?php } ?>
					<!-- ********** -->

				<h4 id="view_keyword" class="tm-keyword">view Keyword</h4>
					<p class="tm-default-value">Default Value: None</p>
					<p class="tm-keyword-description">The <em><b>view</b></em> keyword is one of the keywords you can use to specify the data you would like to print by simply providing the name of the database table you want to print. This example will print all rows and all columns in the tm_example_view view. <em>Note: the tm_example_view has only 4 of the columns (Horse, Breed, Rider, Score) from the tm_example_table and they columns are in a different order and 7 rows.</em></p>
					<!-- Shortcode -->
						<?php $shortcode = '[tablemaster view="tm_example_view"]'; ?>
						<p class="tm-shortcode-example">Example: <?php echo $shortcode ?> </p>
						<?php if ( !is_admin() ) { ?> <div class="tm-example-table"> <?php echo do_shortcode( $shortcode ); ?>  </div> <?php } ?>
					<!-- ********** -->

				<h4 id="columns_keyword" class="tm-keyword">columns Keyword</h4>
					<p class="tm-default-value">Default Value: None</p>
					<p class="tm-keyword-description">The <em><b>columns</b></em> keyword allows you to select the columns from your database table (or view) that you would like to print. When the <em><b>columns</b></em> keyword is used with the <em><b>table</b></em> or <em><b>view</b></em> keywords, you must specify the names of the columns as a comma separated list with NO spaces after each comma, and the names must be specified exactly as they are specified in the actual database table or view. The example below will print 3 columns (Horse, Breed, Score) from the tm_example_view view. However, when the <em><b>columns</b></em> keyword is used with the <em><b>sql</b></em> keyword (described next), you must specify the names of the columns exactly as they will be returned by the query command that you provide (see the example in next section). </p>
					<!-- Shortcode -->
						<?php $shortcode = '[tablemaster view="tm_example_view" columns="Horse,Breed,Score"]'; ?>
						<p class="tm-shortcode-example">Example: <?php echo $shortcode ?> </p>
						<?php if ( !is_admin() ) { ?> <div class="tm-example-table"> <?php echo do_shortcode( $shortcode ); ?>  </div> <?php } ?>
					<!-- ********** -->

				<h4 id="sql_keyword" class="tm-keyword">sql Keyword</h4>
					<p class="tm-default-value">Default Value: None</p>
					<p class="tm-keyword-description">The <em><b>sql</b></em> keyword allows you to specify any valid MySQL command to select the data from your database table (or view) that you would like to print.  The <em><b>sql</b></em> keyword provides you with as much flexibility and/or complexity as you need in order to select the data. However, there is one thing you need to watch out for. The Wordpress visual editor will sometimes turn any &lt; (less than), &lt;&#61; (less than or equal to), &gt; (greater than), &gt;&#61; (greater than or equal to) sign into their equivalent HTML entities. And when that happens, your query command will be invalide. Therefore, TableMaster provides substitutions for these characters that you can use without having to worry about what the Visual Editor will do to your query command.</p>
					<div class="row">
						<div class="col-md-4">
							<table class="tm-html-entities"><tbody>    
								<tr><td>Symbol</td><td>Substitute</td></tr>
								<tr><td>&lt;</td><td>__LT__</td></tr>
								<tr><td>&lt;&#61;</td><td>__LE__</td></tr>
								<tr><td>&gt;</td><td>__GT__</td></tr>
								<tr><td>&gt;&#61;</td><td>__GE__</td></tr>
								<tr><td>&#61;</td><td>__EQ__</td></tr>
								<tr><td>&lt;&gt;</td><td>__NE__</td></tr>
							</tbody></table>
						</div>
					</div>
					<p class="tm-keyword-description">This example will print all rows in the tm_example_table where the Score value is greater than 59.</p>
					<!-- Shortcode -->
						<?php $shortcode = '[tablemaster sql="SELECT * from tm_example_table where Score __GT__ 59"]'; ?>
						<p class="tm-shortcode-example">Example: <?php echo $shortcode ?> </p>
						<?php if ( !is_admin() ) { ?> <div class="tm-example-table"> <?php echo do_shortcode( $shortcode ); ?>  </div> <?php } ?>
					<!-- ********** -->

					<p class="tm-keyword-description">This example will print all rows in the tm_example_long_table that contain scores for the horse named Cirqu du Soleil.</p>
					<!-- Shortcode -->
						<?php $shortcode = '[tablemaster sql="SELECT * from tm_example_long_table where Horse __EQ__ \'Cirque du Soleil\'"]'; ?>
						<p class="tm-shortcode-example">Example: <?php echo $shortcode ?> </p>
						<?php if ( !is_admin() ) { ?> <div class="tm-example-table"> <?php echo do_shortcode( $shortcode ); ?>  </div> <?php } ?>
					<!-- ********** -->

<p class="tm-keyword-description">While the <em><b>columns</b></em> keyword can be used with the <em><b>sql</b></em> keyword, using the <em><b>columns</b></em> keyword with the <em><b>sql</b></em> keyword is useful only in a situation where you need to include a specific column name in your query, but you don't actually want to print that column. This would be true, for example, if you were using the <em><b>link_labels</b></em> and <em><b>link_targets</b></em> keywords (described in the TableMaster Advanced Keywords section). But, here is a simple example to illustrate how the <em><b>columns</b></em> keyword could be used with the <em><b>sql</b></em> keyword. <em>Note: 4 columns are selected by the query, but only 3 columns are printed, and the column names specified with the <em><b>columns</b></em> keyword are the column names as they would be returned by the query, rather than the actual names of the columns in the database table.</em></p>
					<!-- Shortcode -->
						<?php $shortcode = '[tablemaster sql="SELECT Horse as \'My Horse\', Score as \'My Score\', Rider, Test as \'My Test\' FROM tm_example_table" columns="My Horse,My Score,My Test"]'; ?>
						<p class="tm-shortcode-example">Example: <?php echo $shortcode ?> </p>
						<?php if ( !is_admin() ) { ?> <div class="tm-example-table"> <?php echo do_shortcode( $shortcode ); ?>  </div> <?php } ?>
					<!-- ********** -->
 
				<h2 class="tm-title">Step 2: Add your own table styling</h2>
 
				<p class="tm-section-description">Most Wordpress themes come with their own default style for tables. But, if you want to change some aspect of the default style, or change the entire style completely, you can use the keywords in this section. The styles you see used in the remaining examples (and in the example styles section at the end of this guide) are defined in the tablemaster/css/tablemaster.css file.  If you would like to customize any of these styles, you can do so by copying the tablemaster.css file and saving it in a 'css' subdirectory of your theme's folder, and then modifying it however you like.

				<h4 id="class_keyword" class="tm-keyword">class Keyword <?php if( is_admin() ) { ?> <span class="dashicons dashicons-admin-settings" style="font-size:120%;color:red;"></span> <?php } else { ?><i class="color_red fa fa-gear"></i> <?php } ?></h4>
					<p class="tm-default-value">Default Value: None</p>
					<p class="tm-keyword-description">The <em><b>class</b></em> keyword allows you to specify the name of one or more CSS classes to be applied to the entire table.  The value you specify with the <em><b>class</b></em> keyword will be inserted directly into the HTML &lt;table&gt; tag 'class' attribute. For example: &lt;table class="<strong>black-header-gray-alternate-rows</strong>"&gt;.</p>
					<!-- Shortcode -->
						<?php $shortcode = '[tablemaster class="black-header-gray-alternate-rows" table="tm_example_table"]'; ?>
						<p class="tm-shortcode-example">Example: <?php echo $shortcode ?> </p>
						<?php if ( !is_admin() ) { ?> <div class="tm-example-table"> <?php echo do_shortcode( $shortcode ); ?>  </div> <?php } ?>
					<!-- ********** -->

				<h4 id="thead_keyword" class="tm-keyword">thead Keyword <?php if( is_admin() ) { ?> <span class="dashicons dashicons-admin-settings" style="font-size:120%;color:red;"></span> <?php } else { ?><i class="color_red fa fa-gear"></i> <?php } ?></h4>
					<p class="tm-default-value">Default Value: "true"</p>
					<p class="tm-keyword-description">The <em><b>thead</b></em> keyword allows you to enable or disable styling for the table header row. Essentially, if this keyword is true, the &lt;thead&gt;&lt;/thead&gt; HTML tags will be used for the first row of the table. And if your theme or your style supports styling the &lt;thead&gt;&lt;/thead&gt; HTML tags, then, a style will be applied to the header row. However, if your theme or style does not support styling the &lt;thead&gt;&lt;/thead&gt; HTML tags, then, a style will not be applied to the header row even if the <em><b>thead</b></em> keyword is set to true.  If you want the first row of your table to be styled the same as the body of your table, then you should set the <em><b>thead</b></em> keyword to false as shown in the example below. </p>
					<!-- Shortcode -->
						<?php $shortcode = '[tablemaster thead="false" view="tm_example_view"]'; ?>
						<p class="tm-shortcode-example">Example: <?php echo $shortcode ?> </p>
						<?php if ( !is_admin() ) { ?> <div class="tm-example-table"> <?php echo do_shortcode( $shortcode ); ?>  </div> <?php } ?>
					<!-- ********** -->

				<h4 id="nohead_keyword" class="tm-keyword">nohead Keyword <?php if( is_admin() ) { ?> <span class="dashicons dashicons-admin-settings" style="font-size:120%;color:red;"></span> <?php } else { ?><i class="color_red fa fa-gear"></i> <?php } ?></h4>
					<p class="tm-default-value">Default Value: "false"</p>
					<p class="tm-keyword-description">The <em><b>nohead</b></em> keyword allows you to print a table without a header row. <em>Note: the column names do not appear in the first row of this table.</em></p>
					<!-- Shortcode -->
						<?php $shortcode = '[tablemaster nohead="true" view="tm_example_view"]'; ?>
						<p class="tm-shortcode-example">Example: <?php echo $shortcode ?> </p>
						<?php if ( !is_admin() ) { ?> <div class="tm-example-table"> <?php echo do_shortcode( $shortcode ); ?>  </div> <?php } ?>
					<!-- ********** -->

				<h4 id="tfoot_keyword" class="tm-keyword">tfoot Keyword <?php if( is_admin() ) { ?> <span class="dashicons dashicons-admin-settings" style="font-size:120%;color:red;"></span> <?php } else { ?><i class="color_red fa fa-gear"></i> <?php } ?></h4>
					<p class="tm-default-value">Default Value: "false"</p>
					<p class="tm-keyword-description">The <em><b>tfoot</b></em> keyword is similar to the <em><b>thead</b></em> except that it works on the last row of the table, and the default setting is false. If the <em><b>tfoot</b></em> keyword is true, the &lt;tfoot&gt;&lt;/tfoot&gt; HTML tags will be used for the last row of the table. And if your theme or your style supports styling the &lt;tfoot&gt;&lt;/tfoot&gt; HTML tags, then, a style will be applied to the last row. However, if your theme or style does not support styling the &lt;tfoot&gt;&lt;/tfoot&gt; HTML tags, then, a style will not be applied to the last row even if the <em><b>tfoot</b></em> keyword is set to true.  If you want the last row of your table to be styled differently than the body of your table, then you should set the <em><b>tfoot</b></em> keyword to true as shown in the example below. </p>
					<!-- Shortcode -->
						<?php $shortcode = '[tablemaster tfoot="true" view="tm_example_view"]'; ?>
						<p class="tm-shortcode-example">Example: <?php echo $shortcode ?> </p>
						<?php if ( !is_admin() ) { ?> <div class="tm-example-table"> <?php echo do_shortcode( $shortcode ); ?>  </div> <?php } ?>
					<!-- ********** -->

				<h4 id="style_keyword" class="tm-keyword">style Keyword <?php if( is_admin() ) { ?> <span class="dashicons dashicons-admin-settings" style="font-size:120%;color:red;"></span> <?php } else { ?><i class="color_red fa fa-gear"></i> <?php } ?></h4>
					<p class="tm-default-value">Default Value: None</p>
					<p class="tm-keyword-description">The <em><b>style</b></em> keyword allows you to specify css style statements to apply to the entire table. The value you specify with the <em><b>style</b></em> keyword will be inserted directly into the HTML &lt;table&gt; tag 'style' attribute. For example: &lt;table style="background-color:lightgray;"&gt;. The example below inserts a style statement that turns the background of the table to a light gray color.</p>
					
					<!-- Shortcode -->
						<?php $shortcode = '[tablemaster style="background-color:lightgray;" table="tm_example_table"]'; ?>
						<p class="tm-shortcode-example">Example: <?php echo $shortcode ?> </p>
						<?php if ( !is_admin() ) { ?> <div class="tm-example-table"> <?php echo do_shortcode( $shortcode ); ?>  </div> <?php } ?>
					<!-- ********** -->

				<h4 id="css_keyword" class="tm-keyword">css Keyword <?php if( is_admin() ) { ?> <span class="dashicons dashicons-admin-settings" style="font-size:120%;color:red;"></span> <?php } else { ?><i class="color_red fa fa-gear"></i> <?php } ?></h4>
					<p class="tm-default-value">Default Value: None</p>
					<p class="tm-keyword-description">The <em><b>style</b></em> keyword allows you to insert css style statements directly into the HTML. The value specified here will be placed inside &lt;style&gt;&lt;/style&gt; tags directly before the HTML &lt;table&gt; tag. The example will insert a css style statement that all of the text to red.</p>
					<!-- Shortcode -->
						<?php $shortcode = '[tablemaster css="table#tablemaster_table_11.table tbody tr td {color: red;}" table="tm_example_table"]'; ?>
						<p class="tm-shortcode-example">Example: <?php echo $shortcode ?> </p>
						<?php if ( !is_admin() ) { ?> <div class="tm-example-table"> <?php echo do_shortcode( $shortcode ); ?>  </div> <?php } ?>
					<!-- ********** -->

				<h4 id="col_widths_keyword" class="tm-keyword">col_widths Keyword</h4>
					<p class="tm-default-value">Default Value: None</p>
					<p class="tm-keyword-description">The <em><b>col_widths</b></em> keyword allows you to control the relative width of each column. You can specify a percentage of the total width that each column should use. The percentages should be specified in comma separated list, in the column order, and without the '%' sign.  The example below will set the first column to use 20% of the width of the table, the second column to use 20% of the width of the table, the third column to use 10% of the width of the table, and the 4th column to use 50% of the width of the table.</p>
					<!-- Shortcode -->
						<?php $shortcode = '[tablemaster col_widths="20,20,10,50" view="tm_example_view"]'; ?>
						<p class="tm-shortcode-example">Example: <?php echo $shortcode ?> </p>
						<?php if ( !is_admin() ) { ?> <div class="tm-example-table"> <?php echo do_shortcode( $shortcode ); ?>  </div> <?php } ?>
					<!-- ********** -->

				<h4 id="nowrap_keyword" class="tm-keyword">nowrap Keyword</h4>
					<p class="tm-default-value">Default Value: None</p>
					<p class="tm-keyword-description">The <em><b>nowrap</b></em> keyword allows you to control whether or not the text in a column will break to a new line when the column's default width is not wide enough for the text. Referring to the example above, notice that the third column width is set to 10 percent, but the text in the column is broken into 2 lines in the rows that contain "Adult Amateur". The <em><b>nowrap</b></em> keyword can be used to prevent the text from wrapping to a new line. In the example below, the width of the third column is set to 10, but the <em><b>nowrap</b></em> keyword has forced the third column to be wide enough to hold the text without wrapping to a new line. If you wish to prevent more than one column from wrapping text to a new line, the column names must be specified as a comma separated list, with no spaces after the commas, and the names must match names that are printed in the header row, or match the names of the columns in the database if no header row is printed. </p>
					<!-- Shortcode -->
						<?php $shortcode = '[tablemaster nowrap="Rider" col_widths="20,20,10,50" view="tm_example_view"]'; ?>
						<p class="tm-shortcode-example">Example: <?php echo $shortcode ?> </p>
						<?php if ( !is_admin() ) { ?> <div class="tm-example-table"> <?php echo do_shortcode( $shortcode ); ?>  </div> <?php } ?>
					<!-- ********** -->

				<h2 class="tm-title">Step 3: Add JQuery Datatables and Buttons</h2>

				<h4 id="datatables_keyword" class="tm-keyword">datatables Keyword <?php if( is_admin() ) { ?> <span class="dashicons dashicons-admin-settings" style="font-size:120%;color:red;"></span> <?php } else { ?><i class="color_red fa fa-gear"></i> <?php } ?></h4>
					<p class="tm-default-value">Default Value: "false"</p>
					<p class="tm-keyword-description">The <em><b>datatables</b></em> keyword allows you to add the <a href="http://datatables.net">DataTables table plugin in jQuery</a> to your table. DataTables is a very powerful jQuery plugin that adds functionality to sort and search for data, change the number of rows that are displayed and the ability to paginate through long tables. The <em><b>datatables</b></em> keyword requires the <em><b>thead</b></em> keyword to be set to true.</p>
					<!-- Shortcode -->
						<?php $shortcode = '[tablemaster datatables="true" table="tm_example_table"]'; ?>
						<p class="tm-shortcode-example">Example: <?php echo $shortcode ?> </p>
						<?php if ( !is_admin() ) { ?> <div class="tm-example-table"> <?php echo do_shortcode( $shortcode ); ?>  </div> <?php } ?>
					<!-- ********** -->

				<h4 id="rows_keyword" class="tm-keyword">rows Keyword <?php if( is_admin() ) { ?> <span class="dashicons dashicons-admin-settings" style="font-size:120%;color:red;"></span> <?php } else { ?><i class="color_red fa fa-gear"></i> <?php } ?></h4>
					<p class="tm-default-value">Default Value: 10</p>
					<p class="tm-keyword-description">The <em><b>rows</b></em> keyword allows you to change the default number of rows to be displayed by the <a href="http://datatables.net">DataTables table plugin in jQuery</a>. Valid options are "10", "25", "50" and "100". The <em><b>rows</b></em> keyword requires the <em><b>datatables</b></em> keyword to be set to true.</p>
					<!-- Shortcode -->
						<?php $shortcode = '[tablemaster rows="25" datatables="true" table="tm_example_long_table"]'; ?>
						<p class="tm-shortcode-example">Example: <?php echo $shortcode ?> </p>
						<?php if ( !is_admin() ) { ?> <div class="tm-example-table"> <?php echo do_shortcode( $shortcode ); ?>  </div> <?php } ?>
					<!-- ********** -->

				<h4 id="buttons_keyword" class="tm-keyword">buttons Keyword <?php if( is_admin() ) { ?> <span class="dashicons dashicons-admin-settings" style="font-size:120%;color:red;"></span> <?php } else { ?><i class="color_red fa fa-gear"></i> <?php } ?></h4>
					<p class="tm-default-value">Default Value: "false"</p>
					<p class="tm-keyword-description">The <em><b>buttons</b></em> keyword allows you to add the <a href="http://datatables.net">DataTables table plugin in jQuery</a> 'Buttons' extension to your table. The 'Buttons' extension adds "Copy", "CSV", "Excel", "PDF" and "Print" buttons to your table that allow your users to copy the table or save the table in CSV, Excel, or PDF format, or Print the table. The <em><b>buttons</b></em> keyword requires the <em><b>datatables</b></em> keyword to be set to true.</p>
					<!-- Shortcode -->
						<?php $shortcode = '[tablemaster buttons="true" datatables="true" table="tm_example_table"]'; ?>
						<p class="tm-shortcode-example">Example: <?php echo $shortcode ?> </p>
						<?php if ( !is_admin() ) { ?> <div class="tm-example-table"> <?php echo do_shortcode( $shortcode ); ?>  </div> <?php } ?>
					<!-- ********** -->

				<h4 id="button_list_keyword" class="tm-keyword">button_list Keyword <?php if( is_admin() ) { ?> <span class="dashicons dashicons-admin-settings" style="font-size:120%;color:red;"></span> <?php } else { ?><i class="color_red fa fa-gear"></i> <?php } ?></h4>
					<p class="tm-default-value">Default Value: None</p>
					<p class="tm-keyword-description">The <em><b>button_list</b></em> keyword allows you to change the default buttons that are added by the <a href="http://datatables.net">DataTables table plugin in jQuery</a> 'Buttons' extension. The desired buttons must be a comma separated list containing one ore more of the following values: copy, excel, csv, pdf, print. The <em><b>button_list</b></em> keyword requires the <em><b>buttons</b></em> keyword to be set to true.</p>
					<!-- Shortcode -->
						<?php $shortcode = '[tablemaster datatables="true" buttons="true" tfoot="true" button_list="csv,pdf,print" table="tm_example_table"]'; ?>
						<p class="tm-shortcode-example">Example: <?php echo $shortcode ?> </p>
						<?php if ( !is_admin() ) { ?> <div class="tm-example-table"> <?php echo do_shortcode( $shortcode ); ?>  </div> <?php } ?>
					<!-- ********** -->
					
				<h4 id="default_sort_keyword" class="tm-keyword">default_sort Keyword <?php if( is_admin() ) { ?> <span class="dashicons dashicons-admin-settings" style="font-size:120%;color:red;"></span> <?php } else { ?><i class="color_red fa fa-gear"></i> <?php } ?></h4>
					<p class="tm-default-value">Default Value: true</p>
					<p class="tm-keyword-description">The <em><b>default_sort</b></em> keyword allows you to disable the default sorting algorithm used by the <a href="http://datatables.net">DataTables table plugin in jQuery</a>. The <em><b>default_sort</b></em> keyword requires the <em><b>datatables</b></em> keyword to be set to true. If you are using the <em><b>sql</b></em> keyword with an ORDER BY clause, you should set the <em><b>default_sort</b></em> keyword to "false".</p>
					<!-- Shortcode -->
						<?php $shortcode = '[tablemaster default_sort="false" datatables="true" buttons="true" tfoot="true" button_list="csv,pdf,print" sql="SELECT * FROM tm_example_long_table ORDER BY Score ASC"]'; ?>
						<p class="tm-shortcode-example">Example: <?php echo $shortcode ?> </p>
						<?php if ( !is_admin() ) { ?> <div class="tm-example-table"> <?php echo do_shortcode( $shortcode ); ?>  </div> <?php } ?>
					<!-- ********** -->

				<h2 id="advanced_keywords" class="tm-title">TableMaster Advanced Keywords</h2>
					<p class="tm-section-description">This section describes TableMaster keywords that have an uncommon or specialized purpose that do not fall into any of the categories described in Steps 1 to 3 above. 
					
				<h4 id="link_labels_keyword" class="tm-keyword">link_labels and link_targets Keywords</h4>

					<p class="tm-default-value">Default Value: None</p>
					<p class="tm-keyword-description">The first in this category are the <em><b>link_labels</b></em> and <em><b>link_targets</b></em> keywords. These keywords are designed to work together to handle the case where you want one or more of your columns in your database table to be printed as hyper links. For example, you would like to print <a href="http://codehorsesoftware.com">Codehorse Software</a> in a table cell rather than print "http://codehorsesoftware.com" in a table cell.  To do this, you will need to store the 'labels' for the links in one column, and the 'targets (i.e. the 'href') for your links in another column. Then, you can use the <em><b>link_labels</b></em> and <em><b>link_targets</b></em> keywords to control how those columns are printed.</p>
					<p class="tm-keyword-description">Consider the following table 'tm_example_link_table' that contains 3 columns 'Column1', 'Column2' and 'Columns'. Column1 contains a 'label', Column2 contains a hyper link, and Column3 contains a description as shown.</p>
					<!-- Shortcode -->
						<?php $shortcode = '[tablemaster table="tm_example_link_table"]'; ?>
						<p class="tm-shortcode-example">Example: <?php echo $shortcode ?> </p>
						<?php if ( !is_admin() ) { ?> <div class="tm-example-table"> <?php echo do_shortcode( $shortcode ); ?>  </div> <?php } ?>
					<!-- ********** -->
					<p class="tm-keyword-description">Then, you can tell TableMaster to print Column1 as a link by adding the <em><b>link_labels</b></em> and <em><b>link_targets</b></em> keywords. The <em><b>link_labels</b></em> keyword tells TableMaster which column (or columns) contain the labels for the links, and the <em><b>link_targets</b></em> keyword tells TableMaster which column (or columns) contain the URL you want to link the label to as shown below.</p>
					
					<!-- Shortcode -->
						<?php $shortcode = '[tablemaster table="tm_example_link_table" link_labels="Column1" link_targets="Column2"]'; ?>
						<p class="tm-shortcode-example">Example: <?php echo $shortcode ?> </p>
						<?php if ( !is_admin() ) { ?> <div class="tm-example-table"> <?php echo do_shortcode( $shortcode ); ?>  </div> <?php } ?>
					<!-- ********** -->
					<p class="tm-keyword-description">But, now that Column1 is printed as a link, you probably don't need to print Column2 at all.  Here's where the <em><b>columns</b></em> keyword comes in handy.  You can use the <em><b>columns</b></em> keyword to tell TableMaster to print only columns Column1 and Column3 as shown below.</p>
					
					<!-- Shortcode -->
						<?php $shortcode = '[tablemaster table="tm_example_link_table" link_labels="Column1" link_targets="Column2" columns="Column1,Column3"]'; ?>
						<p class="tm-shortcode-example">Example: <?php echo $shortcode ?> </p>
						<?php if ( !is_admin() ) { ?> <div class="tm-example-table"> <?php echo do_shortcode( $shortcode ); ?>  </div> <?php } ?>
					<!-- ********** -->
					<p class="tm-keyword-description">And finally, the column names in this example are not very meaningful, so let's use the <em><b>sql</b></em> keyword to peform a query that returns more meaningful column names for this table.</p>
					
					<!-- Shortcode -->
						<?php $shortcode = '[tablemaster sql="SELECT Column1 as \'Link Label\', Column2, Column3 as \'Link Description\' FROM tm_example_link_table" link_labels="Link Label" link_targets="Column2" columns="Link Label,Link Description"]'; ?>
						<p class="tm-shortcode-example">Example: <?php echo $shortcode ?> </p>
						<?php if ( !is_admin() ) { ?> <div class="tm-example-table"> <?php echo do_shortcode( $shortcode ); ?>  </div> <?php } ?>
					<!-- ********** -->
					
				<h4 id="new_window_keyword" class="tm-keyword">new_window Keyword <?php if( is_admin() ) { ?> <span class="dashicons dashicons-admin-settings" style="font-size:120%;color:red;"></span> <?php } else { ?><i class="color_red fa fa-gear"></i> <?php } ?> </h4>
					<p class="tm-default-value">Default Value: "true"</p>
					<p class="tm-keyword-description">The <em><b>new_window</b></em> keyword only applies when you are using the <em><b>link_labels</b></em> and <em><b>link_targets</b></em> keywords. The <em><b>new_window</b></em>keyword determines whether links are opened in a new browser window (or tab) or whether they are opened in the current window. The example below will open links in the current window.</p>
					<!-- Shortcode -->
						<?php $shortcode = '[tablemaster new_window="false" sql="SELECT Column1 as \'Link Label\', Column2, Column3 as \'Link Description\' FROM tm_example_link_table" link_labels="Link Label" link_targets="Column2" columns="Link Label,Link Description"]'; ?>
						<p class="tm-shortcode-example">Example: <?php echo $shortcode ?> </p>
						<?php if ( !is_admin() ) { ?> <div class="tm-example-table"> <?php echo do_shortcode( $shortcode ); ?>  </div> <?php } ?>
					<!-- ********** -->
					
				<h4 id="pre_table_filter_keyword" class="tm-keyword">pre_table_filter Keyword</h4>
					<p class="tm-default-value">Default Value: None</p>
					<p class="tm-keyword-description">The <em><b>pre_table_filter</b></em> keyword can be used to call a user-defined filter before the table is printed.  In the example below, the user-defined filter calls a function that prints a company logo. A pre-table filter can be used to perform any user-defined processing prior to printing the table. </p>
					<!-- Shortcode -->
						<?php $shortcode = '[tablemaster pre_table_filter="my_filter_name" view="tm_example_view"]'; ?>
						<p class="tm-shortcode-example">Example: <?php echo $shortcode ?> </p>
						<?php if ( !is_admin() ) { ?> <div class="tm-example-table"> <?php echo do_shortcode( $shortcode ); ?>  </div> <?php } ?>
					<!-- ********** -->
					
				<h4 id="post_table_filter_keyword" class="tm-keyword">post_table_filter Keyword</h4>
					<p class="tm-default-value">Default Value: None</p>
					<p class="tm-keyword-description">The <em><b>post_table_filter</b></em> keyword can be used to call a user-defined filter after the table is printed.  In the example below, the user-defined filter calls a function that prints a company logo. A post-table filter can be used to perform any user-defined processing after the table is printed. </p>
					<!-- Shortcode -->
						<?php $shortcode = '[tablemaster post_table_filter="my_filter_name" view="tm_example_view"]'; ?>
						<p class="tm-shortcode-example">Example: <?php echo $shortcode ?> </p>
						<?php if ( !is_admin() ) { ?> <div class="tm-example-table"> <?php echo do_shortcode( $shortcode ); ?>  </div> <?php } ?>
					<!-- ********** -->
				
				<?php if ( !is_admin() ) { ?> <h2 id="example_styles" class="tm-title">Example Table Styles Provided with TableMaster</h2><?php  } ?>
				<?php if ( !is_admin() ) { ?> <p class="tm-keyword-description">Below are samples of the styles provided in the tablemaster/css/tablemaster.css file. </p><?php  } ?>

				<?php if ( !is_admin() ) { ?> <h4 id="css_1" class="tm-keyword">black-header-gray-alternate-rows</h4><?php  } ?>
					<!-- Shortcode -->
						<?php $shortcode = '[tablemaster class="black-header-gray-alternate-rows" table="tm_example_table"]'; ?>
						<?php if ( !is_admin() ) { ?> <div class="tm-example-table"> <?php echo do_shortcode( $shortcode ); ?>  </div> <?php } ?>
					<!-- ********** -->
 
				<?php if ( !is_admin() ) { ?> <h4 id="css_2" class="tm-keyword">blue-header-blue-alternate-rows</h4><?php  } ?>
					<!-- Shortcode -->
						<?php $shortcode = '[tablemaster class="blue-header-blue-alternate-rows" table="tm_example_table"]'; ?>
						<?php if ( !is_admin() ) { ?> <div class="tm-example-table"> <?php echo do_shortcode( $shortcode ); ?>  </div> <?php } ?>
					<!-- ********** -->
 
				<?php if ( !is_admin() ) { ?> <h4 id="css_3" class="tm-keyword">red-header-only</h4><?php  } ?>
					<!-- Shortcode -->
						<?php $shortcode = '[tablemaster class="red-header-only" table="tm_example_table"]'; ?>
						<?php if ( !is_admin() ) { ?> <div class="tm-example-table"> <?php echo do_shortcode( $shortcode ); ?>  </div> <?php } ?>
					<!-- ********** -->
 
				<?php if ( !is_admin() ) { ?> <h4 id="css_4" class="tm-keyword">green-header-alternate-rows</h4><?php  } ?>
					<!-- Shortcode -->
						<?php $shortcode = '[tablemaster class="green-header-alternate-rows" table="tm_example_table"]'; ?>
						<?php if ( !is_admin() ) { ?> <div class="tm-example-table"> <?php echo do_shortcode( $shortcode ); ?>  </div> <?php } ?>
					<!-- ********** -->
 
				<h2 id="trouble_shooting" class="tm-title">Troubleshooting Tips</h2>

				<ul class="tm-tips">
					<li>Be wary of the Wordpress Visual Editor. If you have a super strange problem where the MySQL command is failing, or a keyword is not being recognized, and you think you have specified all of the keywords properly, check the shortcode using the HTML/Text editor and make sure the Wordpress Visual Editor hasn't inserted text styling in the middle of the shortcode. </li>
				</ul>
			</div> <!-- end tablemaster-users-guide -->

			<?php if ( is_admin() ) { ?>
			</div> <!-- wpcontent-body -->
		</div>  <!-- end wrap -->
	
			<?php  } ?>
		
<?php 
	} 	// <!-- end print_users_guide -->
} //<!-- end class TableMaster_Users_Guide -->
} 
?>