<?php

class activitee {

	private $conn;

	public function __construct($dbConnection) {
		$this->conn = $dbConnection;
	}

	public function getActivities(): array {
		$sql = "
			SELECT
				a.id,
				a.title,
				a.description,
				a.activity_type,
				a.status,
				a.activity_date,
				a.activity_time,
				a.location_id,
				l.latitude AS location_latitude,
				l.longitude AS location_longitude,
				l.country AS location_country,
				l.country_code AS location_country_code,
				l.city AS location_city,
				l.postal_code AS location_postal_code,
				l.street AS location_street,
				l.house_number AS location_house_number,
				l.formatted_address AS location_formatted_address,
				a.created_by,
				u.username AS created_by_username,
				a.created_at
			FROM activities a
			LEFT JOIN locations l ON l.id = a.location_id
			LEFT JOIN users u ON u.id = a.created_by
			ORDER BY a.activity_date ASC, a.activity_time ASC, a.id ASC
		";

		$result = $this->conn->query($sql);

		$activities = [];

		while ($row = $result->fetch()) {
			$activities[] = [
				"id" => (int) $row["id"],
				"title" => $row["title"],
				"description" => $row["description"],
				"activity_type" => $row["activity_type"],
				"status" => $row["status"],
				"activity_date" => $row["activity_date"],
				"activity_time" => $row["activity_time"],
				"location" => [
					"id" => $row["location_id"] !== null ? (int) $row["location_id"] : null,
					"latitude" => $row["location_latitude"] !== null ? (float) $row["location_latitude"] : null,
					"longitude" => $row["location_longitude"] !== null ? (float) $row["location_longitude"] : null,
					"country" => $row["location_country"],
					"country_code" => $row["location_country_code"],
					"city" => $row["location_city"],
					"postal_code" => $row["location_postal_code"],
					"street" => $row["location_street"],
					"house_number" => $row["location_house_number"],
					"formatted_address" => $row["location_formatted_address"]
				],
				"created_by" => [
					"id" => $row["created_by"] !== null ? (int) $row["created_by"] : null,
					"username" => $row["created_by_username"]
				],
				"created_at" => $row["created_at"]
			];
		}

		return $activities;
	}
}
