<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lazyloaddata_model extends CI_Model {

  // constructor
	function __construct()
	{
		parent::__construct();
	}

  // Servre side testing
  function courses($limit, $start, $col, $dir, $filter_data)
  {
    $this->db->limit($limit,$start);
    $this->db->order_by($col,$dir);

    // apply the filter data
    // check if the user is admin. Admin can not see the draft courses
    if (strtolower($this->session->userdata('role')) == 'admin') {
        $this->db->where("status !=", 'draft');
    }
    if ($filter_data['selected_category_id'] != 'all') {
      $this->db->where('sub_category_id', $filter_data['selected_category_id']);
    }
    if ($filter_data['selected_instructor_id'] != "all") {
      $this->db->where('user_id', $filter_data['selected_instructor_id']);
    }
    if ($filter_data['selected_price'] != "all") {
      if ($filter_data['selected_price'] == "paid") {
        $this->db->where('is_free_course', null);
      }elseif ($filter_data['selected_price'] == "free") {
        $this->db->where('is_free_course', 1);
      }
    }
    if ($filter_data['selected_status'] != "all") {
      $this->db->where('status', $filter_data['selected_status']);
    }
    $query = $this->db->get('course');
    if($query->num_rows() > 0)
    return $query->result();
    else
    return null;

  }

  function course_search($limit, $start, $search, $col, $dir, $filter_data)
  {
    $this->db->like('title', $search);
    $this->db->limit($limit, $start);
    $this->db->order_by($col, $dir);
    // apply the filter data
    // check if the user is admin. Admin can not see the draft courses
    if (strtolower($this->session->userdata('role')) == 'admin') {
        $this->db->where("status !=", 'draft');
    }
    if ($filter_data['selected_category_id'] != 'all') {
      $this->db->where('sub_category_id', $filter_data['selected_category_id']);
    }
    if ($filter_data['selected_instructor_id'] != "all") {
      $this->db->where('user_id', $filter_data['selected_instructor_id']);
    }
    if ($filter_data['selected_price'] != "all") {
      if ($filter_data['selected_price'] == "paid") {
        $this->db->where('is_free_course', null);
      }elseif ($filter_data['selected_price'] == "free") {
        $this->db->where('is_free_course', 1);
      }
    }
    if ($filter_data['selected_status'] != "all") {
      $this->db->where('status', $filter_data['selected_status']);
    }

    $query = $this->db->get('course');
    if($query->num_rows() > 0)
    return $query->result();
    else
    return null;
  }

  function course_search_count($search)
  {
    $query = $this
    ->db
    ->like('title', $search)
    ->get('course');

    return $query->num_rows();
  }

  function count_all_courses($filter_data = array()) {
    // apply the filter data
    // check if the user is admin. Admin can not see the draft courses
    if (strtolower($this->session->userdata('role')) == 'admin') {
        $this->db->where("status !=", 'draft');
    }
    if ($filter_data['selected_category_id'] != 'all') {
      $this->db->where('sub_category_id', $filter_data['selected_category_id']);
    }

    if ($filter_data['selected_instructor_id'] != "all") {
      $this->db->where('user_id', $filter_data['selected_instructor_id']);
    }
    if ($filter_data['selected_price'] != "all") {
      if ($filter_data['selected_price'] == "paid") {
        $this->db->where('is_free_course', null);
      }elseif ($filter_data['selected_price'] == "free") {
        $this->db->where('is_free_course', 1);
      }
    }
    if ($filter_data['selected_status'] != "all") {
      $this->db->where('status', $filter_data['selected_status']);
    }
    $query = $this->db->get('course');
    return $query->num_rows();
  }


  public function get_all_outstanding_payment_by_course($course_id)
  {
    $sql = "SELECT c.id AS course_id, c.price, c.title, u.id AS user_id, u.first_name, u.last_name, SUM(p.amount) AS paid_amount FROM course c JOIN users u ON c.id = u.course_id JOIN payment p ON p.course_id = c.id WHERE p.user_id = u.id AND u.payment_type_flag IS NOT NULL AND u.course_id IS NOT NULL AND c.id = ? GROUP BY u.course_id, u.id";
    return $this->db->query($sql, array($course_id));
  }
}
