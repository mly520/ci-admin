<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Contact_model extends CI_Model
{
    /**
     * This function is used to get the user listing count
     * @param string $searchText : This is optional search text
     * @return number $count : This is row count
     */
    function contactListingCount($searchText = '')
    {
        $this->db->select('BaseTbl.contactId, BaseTbl.email, BaseTbl.name, BaseTbl.mobile');
        $this->db->from('tbl_contacts as BaseTbl');
        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.email  LIKE '%".$searchText."%'
                            OR  BaseTbl.name  LIKE '%".$searchText."%'
                            OR  BaseTbl.mobile  LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }
        $this->db->where('BaseTbl.isDeleted', 0);
        $query = $this->db->get();
        
        return $query->num_rows();
    }
    
    /**
     * This function is used to get the user listing count
     * @param string $searchText : This is optional search text
     * @param number $page : This is pagination offset
     * @param number $segment : This is pagination limit
     * @return array $result : This is result
     */
    function contactListing($searchText = '', $page, $segment)
    {
        $this->db->select('BaseTbl.contactId, BaseTbl.email, BaseTbl.name, BaseTbl.mobile');
        $this->db->from('tbl_contacts as BaseTbl');
        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.email  LIKE '%".$searchText."%'
                            OR  BaseTbl.name  LIKE '%".$searchText."%'
                            OR  BaseTbl.mobile  LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }
        $this->db->where('BaseTbl.isDeleted', 0);
        $this->db->limit($page, $segment);
        $query = $this->db->get();
        
        $result = $query->result();        
        return $result;
    }

    /**
     * This function is used to add new user to system
     * @return number $insert_id : This is last inserted id
     */
    function addNewUser($userInfo)
    {
        $this->db->trans_start();
        $this->db->insert('tbl_contacts', $userInfo);
        
        $insert_id = $this->db->insert_id();
        
        $this->db->trans_complete();
        
        return $insert_id;
    }
    
    /**
     * This function used to get user information by id
     * @param number $userId : This is user id
     * @return array $result : This is user information
     */
    function getContactInfo($id)
    {
        $this->db->select('contactId, name, email, mobile');
        $this->db->from('tbl_contacts');
        $this->db->where('isDeleted', 0);
        $this->db->where('contactId', $id);
        $query = $this->db->get();

        return $query->result();
    }
    
    
    /**
     * This function is used to update the user information
     * @param array $userInfo : This is users updated information
     * @param number $contactId : This is user id
     */
    function editUser($userInfo, $contactId)
    {
        $this->db->where('contactId', $contactId);
        $this->db->update('tbl_contacts', $userInfo);
        
        return TRUE;
    }
    
    
    
    /**
     * This function is used to delete the user information
     * @param number $contactId : This is user id
     * @return boolean $result : TRUE / FALSE
     */
    function deleteUser($contactId, $userInfo)
    {
        $this->db->where('contactId', $contactId);
        $this->db->update('tbl_contacts', $userInfo);
        
        return $this->db->affected_rows();
    }


    /**
     * This function is used to match users password for change password
     * @param number $contactId : This is user id
     */
    function matchOldPassword($contactId, $oldPassword)
    {
        $this->db->select('contactId, password');
        $this->db->where('contactId', $contactId);
        $this->db->where('isDeleted', 0);
        $query = $this->db->get('tbl_contacts');
        
        $user = $query->result();

        if(!empty($user)){
            if(verifyHashedPassword($oldPassword, $user[0]->password)){
                return $user;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }
    
    /**
     * This function is used to change users password
     * @param number $contactId : This is user id
     * @param array $userInfo : This is user updation info
     */
    function changePassword($contactId, $userInfo)
    {
        $this->db->where('contactId', $contactId);
        $this->db->where('isDeleted', 0);
        $this->db->update('tbl_contacts', $userInfo);
        
        return $this->db->affected_rows();
    }


    /**
     * This function is used to get user login history
     * @param number $contactId : This is user id
     */
    function loginHistoryCount($contactId, $searchText, $fromDate, $toDate)
    {
        $this->db->select('BaseTbl.contactId, BaseTbl.sessionData, BaseTbl.machineIp, BaseTbl.userAgent, BaseTbl.agentString, BaseTbl.platform, BaseTbl.createdDtm');
        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.email  LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }
        if(!empty($fromDate)) {
            $likeCriteria = "DATE_FORMAT(BaseTbl.createdDtm, '%Y-%m-%d' ) >= '".date('Y-m-d', strtotime($fromDate))."'";
            $this->db->where($likeCriteria);
        }
        if(!empty($toDate)) {
            $likeCriteria = "DATE_FORMAT(BaseTbl.createdDtm, '%Y-%m-%d' ) <= '".date('Y-m-d', strtotime($toDate))."'";
            $this->db->where($likeCriteria);
        }
        $this->db->where('BaseTbl.contactId', $contactId);
        $this->db->from('tbl_last_login as BaseTbl');
        $query = $this->db->get();
        
        return $query->num_rows();
    }

    /**
     * This function is used to get user login history
     * @param number $contactId : This is user id
     * @param number $page : This is pagination offset
     * @param number $segment : This is pagination limit
     * @return array $result : This is result
     */
    function loginHistory($contactId, $searchText, $fromDate, $toDate, $page, $segment)
    {
        $this->db->select('BaseTbl.contactId, BaseTbl.sessionData, BaseTbl.machineIp, BaseTbl.userAgent, BaseTbl.agentString, BaseTbl.platform, BaseTbl.createdDtm');
        $this->db->from('tbl_last_login as BaseTbl');
        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.email  LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }
        if(!empty($fromDate)) {
            $likeCriteria = "DATE_FORMAT(BaseTbl.createdDtm, '%Y-%m-%d' ) >= '".date('Y-m-d', strtotime($fromDate))."'";
            $this->db->where($likeCriteria);
        }
        if(!empty($toDate)) {
            $likeCriteria = "DATE_FORMAT(BaseTbl.createdDtm, '%Y-%m-%d' ) <= '".date('Y-m-d', strtotime($toDate))."'";
            $this->db->where($likeCriteria);
        }
        $this->db->where('BaseTbl.contactId', $contactId);
        $this->db->order_by('BaseTbl.id', 'DESC');
        $this->db->limit($page, $segment);
        $query = $this->db->get();
        
        $result = $query->result();        
        return $result;
    }

    /**
     * This function used to get user information by id
     * @param number $contactId : This is user id
     * @return array $result : This is user information
     */
    function getUserInfoById($contactId)
    {
        $this->db->select('contactId, name, email, mobile, roleId');
        $this->db->from('tbl_contacts');
        $this->db->where('isDeleted', 0);
        $this->db->where('contactId', $contactId);
        $query = $this->db->get();
        
        return $query->row();
    }

}

  