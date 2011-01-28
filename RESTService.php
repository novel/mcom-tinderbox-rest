<?php

set_include_path(get_include_path() . PATH_SEPARATOR . "..");

require_once 'core/TinderboxDS.php';

class RESTService {

	function RESTService() {
		$this->tinderboxDS = new TinderboxDS();
	}

	function check_auth($headers) {
		if (array_key_exists('X-Tinderbox-Token', $headers) == TRUE &&
			array_key_exists('X-Tinderbox-User', $headers) == TRUE) {
			$username = $headers['X-Tinderbox-User'];
			$auth_token = $headers['X-Tinderbox-Token'];

			$user = $this->tinderboxDS->getUserByName($username);

			if ($user == null)
				exit($this->_error("Authentication failed"));
	
			$md5_pass = $user->user_password;

			if (md5($username . $md5_pass) != $auth_token)
				exit($this->_error("Authentication failed"));
		} else {
			exit($this->_error("Authentication failed"));
		}
	}

	function listBuilds() {
		$builds = $this->tinderboxDS->getAllBuilds();

		$builds_object = array("builds" => array());

		foreach ($builds as $build) {
			$builds_object["builds"][] = $this->_constructBuild($build);
		}

		return $this->_encode($builds_object);
	}

	function getBuild($id) {
		$build = $this->tinderboxDS->getBuildById($id);

		$builds_object = array("builds" => $this->_constructBuild($build));

		return $this->_encode($builds_object);
	}

	function listQueueEntries() {
		$builds = $this->tinderboxDS->getAllBuilds();

		$queue_entries = array("entries" => array());

		foreach ($builds as $build) {
			$entries = $this->tinderboxDS->getBuildPortsQueueEntries($build->getId());

			foreach ($entries as $entry) {
				$queue_entries["entries"][] = $this->_constructEntry($entry);
			}
		}

		return $this->_encode($queue_entries);
	}

	function getQueueEntry($id) {
		$entry = $this->tinderboxDS->getBuildPortsQueueEntryById($id);

		$entries_object = array("entries" => $this->_constructEntry($entry));

		return $this->_encode($entries_object);
	}

	function addQueueEntry($build_id, $priority, $port_directory, $email_on_completion) {
		$entry = $this->tinderboxDS->createBuildPortsQueueEntry($build_id, $priority, $port_directory,
			1, $email_on_completion);

		$this->tinderboxDS->addBuildPortsQueueEntry($entry);

		return $this->_encode(array("status" => "ok"));
	}

	function getLatestBuildPorts($count, $build_id) {
		if ($build_id == null) {
			/* listing for all builds */
			$activeBuilds = array();
			$builds = $this->tinderboxDS->getBuilds();
			if ($builds) {
				foreach ($builds as $build) {
				//	if (empty($showbuild) || $build->getName() == $showbuild) {
						if ($build->getBuildStatus() == 'PORTBUILD') {
							$activeBuilds[] = $build;
						}
				//	}
				}
			}

			$buildports = array();

			$i = 0;
			foreach ($activeBuilds as $build) {
				$buildport = array();
				$port_version = $build->getBuildCurrentPort();

				$buildport["buildport"]["target_port"] = "N/A";
				$buildport["buildport"]["port_current_version"] = $port_version;

				$build_object = $this->_constructBuild($build);//["build"];
				$buildport["buildport"]["build"] = $build_object; //["build"];

				$current_port = $this->tinderboxDS->getCurrentPortForBuild($build->getId());

				if (!is_null($current_port)) {
					$buildport_object = $this->_constructBuildPort(
						$this->tinderboxDS->getBuildPorts($current_port->getId(),
						$build->getId())
					);

					$buildport["buildport"]["buildport"] = $buildport_object;	
				}

				$build_ports_queue_entries = $this->tinderboxDS->getBuildPortsQueueEntries($build->getId());
				foreach ($build_ports_queue_entries as $build_ports_queue_entry) {
					if ($build_ports_queue_entry->getStatus() == 'PROCESSING') {
						$buildport["buildport"]["target_port"] = $build_ports_queue_entry->getPortDirectory();
						break;
					}
				}

				$buildports["buildports"][] = $buildport;
			}
			
			return $this->_encode($buildports);
		}


	}

	function _encode($data) {
		$response_data = $data;
		$response_data["summary"]["status"] = "ok";

		return json_encode($response_data);
	}

	function _error($message) {
		$response_data = array("summary" => array());
		$response_data["summary"]["status"] = "fail";
		$response_data["summary"]["reason"] = $message;

		return json_encode($response_data);
	}

	function _constructBuild($build) {
		$build_object = array("build" => array());

		$build_object["build"]["id"] = $build->getId();
		$build_object["build"]["name"] = $build->getName();
		$build_object["build"]["jail"]["id"] = $build->getJailId();
		$build_object["build"]["portstree"]["id"] = $build->getPortsTreeId();
		$build_object["build"]["description"] = $build->getDescription();
		$build_object["build"]["status"] = $build->getBuildStatus();
		$build_object["build"]["currentport"] = $build->getBuildCurrentPort();
		$build_object["build"]["updated"] = $build->getBuildLastUpdated();
		$build_object["build"]["remakecount"] = $build->getBuildRemakeCount();

		return $build_object;
	}

	function _constructEntry($entry) {
		$entry_object = array("entry" => array());

		$entry_object["entry"]["id"] = $entry->getBuildPortsQueueId();
		$entry_object["entry"]["portdirectory"] = $entry->getPortDirectory();
		$entry_object["entry"]["priority"] = $entry->getPriority();
		$entry_object["entry"]["buildname"] = $entry->getBuildName();
		$entry_object["entry"]["username"] = $entry->getUserName();
		$entry_object["entry"]["status"] = $entry->getStatus();
		$entry_object["entry"]["user"]["id"] = $entry->getUserId();
		$entry_object["entry"]["build"]["id"] = $entry->getBuildId();
		$entry_object["entry"]["enqueued"] = $entry->getEnqueueDate();
		$entry_object["entry"]["completed"] = $entry->getCompletionDate();

		return $entry_object;
	}

	function _constructPort($port) {
		$port_object = array();

		$port_object["port"]["id"] = $port->getId();
		$port_object["port"]["directory"] = $port->getDirectory();
		$port_object["port"]["name"] = $port->getName();
		$port_object["port"]["maintainer"] = $port->getMaintainer();
		$port_object["port"]["comment"] = $port->getComment();

		return $port_object;
	}

	function _constructBuildPort($buildport) {
		$buildport_object = array();

		$buildport_object["buildport"]["id"] = $buildport->getId();
		$buildport_object["buildport"]["directory"] = $buildport->getDirectory();
		$buildport_object["buildport"]["name"] = $buildport->getName();
		$buildport_object["buildport"]["maintainer"] = $buildport->getMaintainer();
		$buildport_object["buildport"]["comment"] = $buildport->getComment();
		$buildport_object["buildport"]["last_built"] = $buildport->getLastBuilt();
		$buildport_object["buildport"]["last_status"] = $buildport->getLastStatus();
		$buildport_object["buildport"]["last_successful_built"] = $buildport->getLastSuccessfulBuilt();
		$buildport_object["buildport"]["last_failed_dependency"] = $buildport->getLastFailedDep();
		$buildport_object["buildport"]["last_run_duration"] = $buildport->getLastRunDuration();
		$buildport_object["buildport"]["last_fail_reason"] = $buildport->getLastFailReason();
		$buildport_object["buildport"]["last_built_version"] = $buildport->getLastBuiltVersion();

		return $buildport_object;
	}


}

?>
