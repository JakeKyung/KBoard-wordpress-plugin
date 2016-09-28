<?php
/**
 * KBoard 댓글 리스트 테이블
 * @link www.cosmosfarm.com
 * @copyright Copyright 2013 Cosmosfarm. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl.html
 */
class KBCommentListTable extends WP_List_Table {
	
	var $list;
	var $url;
	
	public function __construct(){
		parent::__construct();
		
		$this->list = new KBCommentList();
		$this->url = new KBUrl();
	}
	
	public function prepare_items(){
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = array();
		$this->_column_headers = array($columns, $hidden, $sortable);
		
		$keyword = isset($_GET['s'])?esc_attr($_GET['s']):'';
		
		$this->list->rpp = 20;
		$this->list->page = $this->get_pagenum();
		$this->list->initWithKeyword($keyword);
		$this->items = $this->list->resource;
		
		$this->set_pagination_args(array('total_items'=>$this->list->total, 'per_page'=>$this->list->rpp));
	}
	
	public function get_table_classes(){
		$classes = parent::get_table_classes();
		$classes[] = 'kboard';
		$classes[] = 'kboard-comments-list';
		return $classes;
	}
	
	public function no_items(){
		echo __('No comment found.', 'kboard');
	}
	
	public function get_columns(){
		return array(
				'cb' => '<input type="checkbox">',
				'board_name' => __('게시판', 'kboard'),
				'user_display' => __('작성자', 'kboard'),
				'content' => __('내용', 'kboard'),
				'date' => __('일자', 'kboard')
		);
	}
	
	function get_bulk_actions(){
		return array(
				'delete' => __('삭제', 'kboard')
		);
	}
	
	public function display_rows(){
		foreach($this->items as $item){
			$this->single_row($item);
		}
	}
	
	public function single_row($item){
		$board = new KBoard();
		$board->initWithContentUID($item->content_uid);
		
		$edit_url = admin_url("admin.php?page=kboard_list&board_id={$board->id}");
		
		echo '<tr data-uid="'.$item->uid.'">';
		
		echo '<th scope="row" class="check-column">';
		echo '<input type="checkbox" name="comment_uid[]" value="'.$item->uid.'">';
		echo '</th>';
		
		echo '<td><a href="'.$edit_url.'" title="'.__('편집', 'kboard').'" style="display:block">';
		echo $board->board_name;
		echo '</a></td>';
		
		echo '<td>';
		if($item->user_uid){
			echo '<a href="'.admin_url('user-edit.php?user_id='.$item->user_uid).'">'.$item->user_display.'</a>';
		}
		else{
			echo $item->user_display;
		}
		echo '</td>';
		
		echo '<td>';
		echo $item->content.' - <a href="'.$this->url->getDocumentRedirect($item->content_uid).'" titlt="페이지에서 보기" onclick="window.open(this.href);return false;">페이지에서 보기</a>';
		echo '</td>';
		
		echo '<td>';
		echo date('Y-m-d H:i:s', strtotime($item->created));
		echo '</td>';
		
		echo '</tr>';
	}
	
	public function search_box($text, $input_id){
	?>
	<p class="search-box">
		<input type="search" id="<?php echo $input_id?>" name="s" value="<?php _admin_search_query()?>">
		<?php submit_button($text, 'button', false, false, array('id'=>'search-submit'))?>
	</p>
	<?php }
}
?>