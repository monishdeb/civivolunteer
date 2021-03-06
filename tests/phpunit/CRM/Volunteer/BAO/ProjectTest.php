<?php

require_once 'VolunteerTestAbstract.php';

/**
 * Test class for Volunteer Project BAO - volunteer_project
 */
class CRM_Volunteer_BAO_ProjectTest extends VolunteerTestAbstract {

  /**
   * Clean table civicrm_volunteer_project
   */
  function setUp() {
    $this->quickCleanup(array('civicrm_volunteer_project', 'civicrm_volunteer_need'));
    parent::setUp();
  }

  function testProjectCreate() {
    $params = array(
      'entity_id' => 1,
      'entity_table' => 'civicrm_event',
    );

    $project = CRM_Volunteer_BAO_Project::create($params);
    $this->assertObjectHasAttribute('id', $project);
  }

  function testProjectDisable() {
    $project = CRM_Core_DAO::createTestObject('CRM_Volunteer_BAO_Project', array('is_active' => 1));
    $this->assertEquals(1, $project->is_active, 'Failed to prepopulate active Volunteer Project');
    $project->disable();
    $this->assertEquals(0, $project->is_active, 'Failed to disable Volunteer Project');
  }

  function testProjectEnable() {
    $project = CRM_Core_DAO::createTestObject('CRM_Volunteer_BAO_Project', array('is_active' => 0));
    $this->assertEquals(0, $project->is_active, 'Failed to prepopulate inactive Volunteer Project');
    $project->enable();
    $this->assertEquals(1, $project->is_active, 'Failed to enable Volunteer Project');
  }

  function testProjectRetrieve() {
    $project = CRM_Core_DAO::createTestObject('CRM_Volunteer_BAO_Project');
    $this->assertObjectHasAttribute('id', $project, 'Failed to prepopulate Volunteer Project');

    $projectRetrieved = CRM_Volunteer_BAO_Project::retrieve(array('id' => $project->id));
    $this->assertNotEmpty($projectRetrieved);
  }

  /**
   * Test helper method isOff, which should return TRUE passed an "off" value
   */
  function testProjectIsOff() {
    $this->assertTrue(CRM_Volunteer_BAO_Project::isOff(FALSE));
    $this->assertTrue(CRM_Volunteer_BAO_Project::isOff(0));
    $this->assertTrue(CRM_Volunteer_BAO_Project::isOff('0'));
  }

  /**
   * A project should inherit the title of its associated entity; effectively we
   * are testing our magic __get() method and its delegate _get_title();
   */
  function testGetEventProjectTitle() {
    $title = 'CiviVolunteer Unit Testing Sprint';
    $entity_table = 'civicrm_event';

    // create Event with specified title
    $event = CRM_Core_DAO::createTestObject('CRM_Event_BAO_Event', array('title' => $title));
    $this->assertEquals($title, $event->title, 'Failed to prepopulate named Event');

    // create Project associated with our Event
    $params = array(
      'entity_id' => $event->id,
      'entity_table' => $entity_table,
    );
    $project = CRM_Core_DAO::createTestObject('CRM_Volunteer_BAO_Project', $params);

    // test project title
    $this->assertEquals($title, $project->title, 'Project title does not match associated Event title');
  }

  function testProjectRetrieveByID () {
    $project = CRM_Core_DAO::createTestObject('CRM_Volunteer_BAO_Project');
    $this->assertObjectHasAttribute('id', $project, 'Failed to prepopulate Volunteer Project');

    $projectRetrieved = CRM_Volunteer_BAO_Project::retrieveByID($project->id);

    // note: a strict comparison doesn't work: the first value is an int and the
    // second is a string; not sure where this occurs, but seems worth a look...
    $this->assertTrue($project->id == $projectRetrieved->id, 'CRM_Volunteer_BAO_Project::retrieveByID failed');
  }

  /**
   * Tests magic __get for needs
   */
  function testProjectGetNeeds() {
    $project = CRM_Core_DAO::createTestObject('CRM_Volunteer_BAO_Project');
    $this->assertObjectHasAttribute('id', $project, 'Failed to prepopulate Volunteer Project');

    // attach need to project
    $need = CRM_Core_DAO::createTestObject('CRM_Volunteer_BAO_Need', array(
      'is_active' => 1,
      'project_id' => $project->id,
      'visibility_id' => CRM_Core_OptionGroup::getValue('visibility', 'public', 'name'),
    ));
    $this->assertObjectHasAttribute('id', $need, 'Failed to prepopulate Volunteer Need');

    $test = $project->needs;
    $this->assertCount(1, $test);
  }

  /**
   * Tests magic __isset for needs
   */
  function testProjectIssetNeeds() {
    $project = CRM_Core_DAO::createTestObject('CRM_Volunteer_BAO_Project');
    $this->assertObjectHasAttribute('id', $project, 'Failed to prepopulate Volunteer Project');

    // attach need to project
    $need = CRM_Core_DAO::createTestObject('CRM_Volunteer_BAO_Need', array(
      'is_active' => 1,
      'project_id' => $project->id,
      'visibility_id' => CRM_Core_OptionGroup::getValue('visibility', 'public', 'name'),
    ));
    $this->assertObjectHasAttribute('id', $need, 'Failed to prepopulate Volunteer Need');

    $this->assertTrue(isset($project->needs));
  }

  /**
   * Tests magic __isset for needs
   */
  function testProjectEmptyNeeds() {
    $project = CRM_Core_DAO::createTestObject('CRM_Volunteer_BAO_Project');
    $this->assertObjectHasAttribute('id', $project, 'Failed to prepopulate Volunteer Project');

    // attach need to project
    $need = CRM_Core_DAO::createTestObject('CRM_Volunteer_BAO_Need', array(
      'is_active' => 1,
      'project_id' => $project->id,
      'visibility_id' => CRM_Core_OptionGroup::getValue('visibility', 'public', 'name'),
    ));
    $this->assertObjectHasAttribute('id', $need, 'Failed to prepopulate Volunteer Need');

    $this->assertFalse(empty($project->needs));
  }

  /**
   * Tests magic __get for needs
   */
  function testProjectGetRoles() {
    $role_id = 2;
    $project = CRM_Core_DAO::createTestObject('CRM_Volunteer_BAO_Project');
    $this->assertObjectHasAttribute('id', $project, 'Failed to prepopulate Volunteer Project');

    // attach need to project
    $need = CRM_Core_DAO::createTestObject('CRM_Volunteer_BAO_Need', array(
      'is_active' => 1,
      'is_flexible' => 0,
      'project_id' => $project->id,
      'role_id' => $role_id,
      'visibility_id' => CRM_Core_OptionGroup::getValue('visibility', 'public', 'name'),
    ));
    $this->assertObjectHasAttribute('id', $need, 'Failed to prepopulate Volunteer Need');

    $test = $project->roles;
    $this->assertArrayKeyExists($role_id, $test);
  }

  /**
   * Tests magic __isset for needs
   */
  function testProjectIssetRoles() {
    $project = CRM_Core_DAO::createTestObject('CRM_Volunteer_BAO_Project');
    $this->assertObjectHasAttribute('id', $project, 'Failed to prepopulate Volunteer Project');

    // attach need to project
    $need = CRM_Core_DAO::createTestObject('CRM_Volunteer_BAO_Need', array(
      'is_active' => 1,
      'is_flexible' => 0,
      'project_id' => $project->id,
      'visibility_id' => CRM_Core_OptionGroup::getValue('visibility', 'public', 'name'),
    ));
    $this->assertObjectHasAttribute('id', $need, 'Failed to prepopulate Volunteer Need');

    $this->assertTrue(isset($project->roles));
  }

  /**
   * Tests magic __isset for needs
   */
  function testProjectEmptyRoles() {
    $project = CRM_Core_DAO::createTestObject('CRM_Volunteer_BAO_Project');
    $this->assertObjectHasAttribute('id', $project, 'Failed to prepopulate Volunteer Project');

    // attach need to project
    $need = CRM_Core_DAO::createTestObject('CRM_Volunteer_BAO_Need', array(
      'is_active' => 1,
      'project_id' => $project->id,
      'visibility_id' => CRM_Core_OptionGroup::getValue('visibility', 'public', 'name'),
    ));
    $this->assertObjectHasAttribute('id', $need, 'Failed to prepopulate Volunteer Need');

    $this->assertFalse(empty($project->roles));
  }

  /**
   * Tests magic __get for needs
   */
  function testProjectGetShifts() {
    $role_id = 2;
    $project = CRM_Core_DAO::createTestObject('CRM_Volunteer_BAO_Project');
    $this->assertObjectHasAttribute('id', $project, 'Failed to prepopulate Volunteer Project');

    // attach need to project
    $need = CRM_Core_DAO::createTestObject('CRM_Volunteer_BAO_Need', array(
      'is_active' => 1,
      'is_flexible' => 0,
      'project_id' => $project->id,
      'role_id' => $role_id,
      'visibility_id' => CRM_Core_OptionGroup::getValue('visibility', 'public', 'name'),
    ));
    $this->assertObjectHasAttribute('id', $need, 'Failed to prepopulate Volunteer Need');

    $test = $project->shifts;
    $this->assertArrayKeyExists($need->id, $test);
    $this->assertArrayKeyExists('role_id', $test[$need->id]);
    $this->assertEquals($role_id, $test[$need->id]['role_id']);
    $this->assertArrayKeyExists('label', $test[$need->id]);
  }

  /**
   * Tests magic __isset for needs
   */
  function testProjectIssetShifts() {
    $project = CRM_Core_DAO::createTestObject('CRM_Volunteer_BAO_Project');
    $this->assertObjectHasAttribute('id', $project, 'Failed to prepopulate Volunteer Project');

    // attach need to project
    $need = CRM_Core_DAO::createTestObject('CRM_Volunteer_BAO_Need', array(
      'is_active' => 1,
      'project_id' => $project->id,
      'visibility_id' => CRM_Core_OptionGroup::getValue('visibility', 'public', 'name'),
    ));
    $this->assertObjectHasAttribute('id', $need, 'Failed to prepopulate Volunteer Need');

    $this->assertTrue(isset($project->shifts));
  }

  /**
   * Tests magic __isset for needs
   */
  function testProjectEmptyShifts() {
    $project = CRM_Core_DAO::createTestObject('CRM_Volunteer_BAO_Project');
    $this->assertObjectHasAttribute('id', $project, 'Failed to prepopulate Volunteer Project');

    // attach need to project
    $need = CRM_Core_DAO::createTestObject('CRM_Volunteer_BAO_Need', array(
      'is_active' => 1,
      'project_id' => $project->id,
      'visibility_id' => CRM_Core_OptionGroup::getValue('visibility', 'public', 'name'),
    ));
    $this->assertObjectHasAttribute('id', $need, 'Failed to prepopulate Volunteer Need');

    $this->assertFalse(empty($project->shifts));
  }
}