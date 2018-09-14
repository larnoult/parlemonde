<?php
/**
 * API Keys Table Class
 *
 * @package     MMP
 * @since       2.7
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Load WP_List_Table if not loaded
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * MMP_API_Keys_Table Class
 * Renders the API Keys table
 * @since 2.7
 */
class MMP_API_Keys_Table extends WP_List_Table {

	/**
	 * @var int Number of items per page
	 * @since 2.7
	 */
	public $per_page = 20;

	/**
	 * @var object Query results
	 * @since 2.7
	 */
	private $keys;

	/**
	 * Get things started
	 *
	 * @since 2.7
	 * @see WP_List_Table::__construct()
	 */
	public function __construct() {
		global $status, $page;

		// Set parent defaults
		parent::__construct( array(
			'singular'  => __( 'API Key', 'lmm' ),
			'plural'    => __( 'API Keys', 'lmm' ),
			'ajax'      => false,
		) );

		$this->query();
	}

	/**
	 * Gets the name of the primary column.
	 *
	 * @since 2.7
	 * @access protected
	 *
	 * @return string Name of the primary column.
	 */
	protected function get_primary_column_name() {
		return 'user';
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @access public
	 * @since 2.7
	 *
	 * @param array $item Contains all the data of the keys
	 * @param string $column_name The name of the column
	 *
	 * @return string Column Name
	 */
	public function column_default( $item, $column_name ) {
		return $item[ $column_name ];
	}

	/**
	 * Displays the public key rows
	 *
	 * @access public
	 * @since 2.7
	 *
	 * @param array $item Contains all the data of the keys
	 * @param string $column_name The name of the column
	 *
	 * @return string Column Name
	 */
	public function column_key( $item ) {
		return '<input readonly="readonly" type="text" class="large-text" value="' . esc_attr( $item[ 'key' ] ) . '"/>';
	}

	/**
	 * Displays the token rows
	 *
	 * @access public
	 * @since 2.7
	 *
	 * @param array $item Contains all the data of the keys
	 * @param string $column_name The name of the column
	 *
	 * @return string Column Name
	 */
	public function column_token( $item ) {
		return '<input readonly="readonly" type="text" class="large-text" value="' . esc_attr( $item[ 'token' ] ) . '"/>';
	}

	/**
	 * Displays the secret key rows
	 *
	 * @access public
	 * @since 2.7
	 *
	 * @param array $item Contains all the data of the keys
	 * @param string $column_name The name of the column
	 *
	 * @return string Column Name
	 */
	public function column_secret( $item ) {
		return '<input readonly="readonly" type="text" class="large-text" value="' . esc_attr( $item[ 'secret' ] ) . '"/>';
	}

	/**
	 * Renders the column for the user field
	 *
	 * @access public
	 * @since 2.7
	 * @return void
	 */
	public function column_user( $item ) {

		$actions = array();

		$actions['reissue'] = sprintf(
			'<a href="%s" class="mmp-regenerate-api-key">%s</a>',
			esc_url( wp_nonce_url( add_query_arg( array( 'user_id' => $item['id'], 'mmp_action' => 'process_api_key', 'mmp_api_process' => 'regenerate' ) ), 'mmp-api-nonce' ) ),
			__( 'Reissue', 'lmm' )
		);
		$confirm_revoke = 'onclick="if ( confirm( \''. esc_attr__('Are you sure that you want to revoke this API key?','lmm') .'\' ) ) { return true;}return false;"';
		$actions['revoke'] = sprintf(
			'<a href="%s" class="mmp-revoke-api-key" style="color: #a00" '. $confirm_revoke .'>%s</a>',
			esc_url( wp_nonce_url( add_query_arg( array( 'user_id' => $item['id'], 'mmp_action' => 'process_api_key', 'mmp_api_process' => 'revoke' ) ), 'mmp-api-nonce' ) ),
			__( 'Revoke', 'lmm' )
		);

		$actions = apply_filters( 'mmp_api_row_actions', array_filter( $actions ) );

		return sprintf('%1$s %2$s', $item['user'], $this->row_actions( $actions ) );
	}

	/**
	 * Retrieve the table columns
	 *
	 * @access public
	 * @since 2.7
	 * @return array $columns Array of all the list table columns
	 */
	public function get_columns() {
		$columns = array(
			'user'   => __( 'Username', 'lmm' ),
			'key'    => __( 'Public Key', 'lmm' ),
			'token'  => __( 'Secret Token', 'lmm' ),
			//'secret' => __( 'Secret Key', 'lmm' ),
		);

		return $columns;
	}

	/**
	 * Display the key generation form
	 *
	 * @access public
	 * @since 2.7
	 * @return void
	 */
	function bulk_actions( $which = '' ) {
		// These aren't really bulk actions but this outputs the markup in the right place
		static $mmp_api_is_bottom;

		if( $mmp_api_is_bottom ) {
			return;
		}
		?>
		<form method="post" action="<?php echo admin_url( 'admin.php?page=leafletmapsmarker_apis' ); ?>">
			<input type="hidden" name="mmp_action" value="process_api_key" />
			<input type="hidden" name="mmp_api_process" value="generate" />
			<?php wp_nonce_field( 'mmp-api-nonce' ); ?>
			<?php $all_users = get_users(); ?>
			<select name="user_id" id="mmp_select_user" style="width:200px !important;">
				<?php foreach($all_users as $user): ?>
						<option value="<?php echo $user->ID; ?>"><?php echo $user->user_login; ?></option>
				<?php endforeach; ?>
			</select>

			<?php submit_button( __( 'Generate new REST API keys for selected user', 'lmm' ), 'secondary', 'submit', false ); ?>
		</form>
		<?php
		$mmp_api_is_bottom = true;
	}
	/**
	 * Retrieve the current page number
	 *
	 * @access public
	 * @since 2.7
	 * @return int Current page number
	 */
	public function get_paged() {
		return isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
	}

	/**
	 * Performs the key query
	 *
	 * @access public
	 * @since 2.7
	 * @return void
	 */
	public function query() {
		$users    = get_users( array(
			'meta_key' => 'leafletmapsmarker_mmp_user_secret_key',
			'number'     => $this->per_page,
			'offset'     => $this->per_page * ( $this->get_paged() - 1 ),
		) );
		$keys     = array();

		foreach( $users as $user ) {
			$keys[$user->ID]['id']     = $user->ID;
			$keys[$user->ID]['email']  = $user->user_email;
			$keys[$user->ID]['user']   = '<a href="' . add_query_arg( 'user_id', $user->ID, 'user-edit.php' ) . '"><strong>' . $user->user_login . '</strong></a>';

			$keys[$user->ID]['key']    = MMP_RESTAPI::get_user_public_key( $user->ID );
			$keys[$user->ID]['secret'] = MMP_RESTAPI::get_user_secret_key( $user->ID );
			$keys[$user->ID]['token']  = MMP_RESTAPI::get_token( $user->ID );
		}

		return $keys;
	}



	/**
	 * Retrieve count of total users with keys
	 *
	 * @access public
	 * @since 2.7
	 * @return int
	 */
	public function total_items() {
		global $wpdb;

		if( ! get_transient( 'leafletmapsmarker_mmp_total_api_keys' ) ) {
			$total_items = $wpdb->get_var( "SELECT count(user_id) FROM $wpdb->usermeta WHERE meta_key='leafletmapsmarker_mmp_user_secret_key'" );

			set_transient( 'leafletmapsmarker_mmp_total_api_keys', $total_items, 60 * 60 );
		}
		return get_transient( 'leafletmapsmarker_mmp_total_api_keys' );
	}

	/**
	 * Setup the final data for the table
	 *
	 * @access public
	 * @since 2.7
	 * @return void
	 */
	public function prepare_items() {
		$columns = $this->get_columns();

		$hidden = array(); // No hidden columns
		$sortable = array(); // Not sortable... for now

		$this->_column_headers = array( $columns, $hidden, $sortable, 'id' );

		$data = $this->query();

		$total_items = $this->total_items();

		$this->items = $data;

		$this->set_pagination_args( array(
				'total_items' => $total_items,
				'per_page'    => $this->per_page,
				'total_pages' => ceil( $total_items / $this->per_page ),
			)
		);
	}

    /**
     * Whether the table has items to display or not
     *
     * @since 2.7
     * @access public
     * @return bool
     */
    public function has_items() {
        return true;
    }
}
