Если сеанс только что стартовал, очистить таблицы urls и url_checks:

TRUNCATE url_checks;
TRUNCATE urls CASCADE;

Если таблиц urls и url_checks не существует, то создать их:
			 
											 
CREATE TABLE urls (id bigint PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
                   name character varying(255),
				   created_at timestamp default current_timestamp
        );
CREATE TABLE IF NOT EXISTS url_checks (
				id bigint PRIMARY KEY GENERATED ALWAYS AS IDENTITY, 
				url_id bigint REFERENCES urls (id),
				status_code integer, 
				h1 character varying(255), 
				title character varying(255), 
				description character varying(255), 
				created_at timestamp);												 