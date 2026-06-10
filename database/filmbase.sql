CREATE TABLE `users` (
  `id`         INT(11)      NOT NULL AUTO_INCREMENT,
  `firstname`  VARCHAR(100) NOT NULL,
  `lastname`   VARCHAR(100) NOT NULL,
  `email`      VARCHAR(255) NOT NULL UNIQUE,
  `password`   VARCHAR(255) NOT NULL,
  `country`    VARCHAR(100) NOT NULL DEFAULT '',
  `role`       ENUM('admin','user') NOT NULL DEFAULT 'user',
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `news` (
  `id`         INT(11)      NOT NULL AUTO_INCREMENT,
  `user_id`    INT(11)      NOT NULL,
  `title`      VARCHAR(255) NOT NULL,
  `body`       TEXT         NOT NULL,
  `image`      VARCHAR(255) NOT NULL DEFAULT '',
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `contacts` (
  `id`         INT(11)      NOT NULL AUTO_INCREMENT,
  `firstname`  VARCHAR(100) NOT NULL,
  `lastname`   VARCHAR(100) NOT NULL,
  `email`      VARCHAR(255) NOT NULL,
  `country`    VARCHAR(100) NOT NULL DEFAULT '',
  `newsletter` TINYINT(1)   NOT NULL DEFAULT 0,
  `subject`    VARCHAR(255) NOT NULL,
  `message`    TEXT         NOT NULL,
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `gallery` (
  `id`         INT(11)      NOT NULL AUTO_INCREMENT,
  `title`      VARCHAR(255) NOT NULL,
  `image`      VARCHAR(255) NOT NULL,
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `users` (`firstname`, `lastname`, `email`, `password`, `country`, `role`, `created_at`) VALUES
('Admin',  'One',   'admin@filmbase.com',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Croatia', 'admin', '2024-01-01 10:00:00'),
('Admin',  'Two',   'admin2@filmbase.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Germany', 'admin', '2024-01-02 11:00:00'),
('John',   'Smith', 'john@example.com',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'USA',     'user',  '2024-01-10 09:00:00'),
('Maria',  'Jones', 'maria@example.com',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'France',  'user',  '2024-01-15 14:00:00');

INSERT INTO `news` (`user_id`, `title`, `body`, `image`, `created_at`) VALUES
(1, 'The Godfather Turns 50',
 'Francis Ford Coppola''s masterpiece The Godfather celebrated its 50th anniversary this year with a stunning 4K restoration that brought new life to one of cinema''s greatest achievements. The film, starring Marlon Brando and Al Pacino, was released in 1972 and went on to win three Academy Awards including Best Picture. The restoration team spent over two years meticulously cleaning and enhancing every frame, working directly from the original camera negatives. Audiences around the world were treated to special theatrical screenings that sold out within hours of tickets going on sale. Critics and fans alike praised the care taken to preserve the film''s original look while benefiting from modern technology.',
 'news1.jpg', '2024-02-10 09:00:00'),

(1, 'Cannes 2024: Top Films to Watch',
 'The Cannes Film Festival returns this May with one of its most anticipated lineups in recent memory. Among the films competing for the Palme d''Or are works from acclaimed directors across Europe, Asia, and the Americas. Festival director Thierry Frémaux unveiled the selection at a packed press conference in Paris, describing the lineup as a celebration of bold and daring cinema. This year''s jury, chaired by a legendary actress, will face the difficult task of choosing from over twenty competing films. Industry insiders are buzzing about several titles that have already generated significant awards season predictions. FilmBase will be covering the event daily with reviews, interviews, and behind-the-scenes content.',
 'news2.jpg', '2024-03-15 10:30:00'),

(2, 'Best Sci-Fi Films of the Decade',
 'Science fiction has experienced a remarkable renaissance over the past ten years, producing some of the most ambitious and visually stunning films in the genre''s history. From intimate character studies set against vast cosmic backdrops to large-scale epics exploring the fate of humanity, filmmakers have pushed the boundaries of what is possible both technically and narratively. Films like Arrival, Dune, and Everything Everywhere All at Once have redefined what mainstream audiences expect from science fiction storytelling. Independent productions have also thrived, with streaming platforms providing budgets and distribution previously unavailable to smaller studios. Our editorial team has compiled a definitive ranking of the decade''s best sci-fi offerings, complete with detailed analysis and streaming availability.',
 'news3.jpg', '2024-04-01 08:00:00'),

(2, 'Interview: Behind the Lens with a Cinematographer',
 'We sat down with one of Hollywood''s most sought-after cinematographers to discuss the craft of visual storytelling, the transition from film to digital, and what it takes to light a scene that will be remembered for generations. With credits on over thirty feature films and countless awards nominations, his perspective on the industry offers rare insight into the invisible art that makes movies magical. He spoke candidly about the challenges of working with demanding directors, the importance of preparation, and why he still shoots tests on 35mm film despite having access to the most advanced digital cameras available today. The conversation ranged from technical details about lens choices to deeply personal reflections on why he chose this career.',
 'news4.jpg', '2024-04-20 13:00:00'),

(1, 'Summer Blockbuster Preview 2024',
 'Hollywood is gearing up for one of the most competitive summer seasons in years, with major studios releasing a string of highly anticipated blockbusters between May and August. Sequels, reboots, and original properties will all compete for the same audience dollars in a marketplace that has steadily recovered since the disruptions of recent years. Early tracking data suggests several films could challenge box office records set before the pandemic. Studios are investing heavily in marketing campaigns that span social media, traditional advertising, and immersive fan experiences at conventions worldwide. FilmBase has compiled a month-by-month breakdown of every major release, along with early buzz from test screenings and industry events, to help you plan your cinema visits for the season ahead.',
 'news5.jpg', '2024-05-01 11:00:00');

INSERT INTO `gallery` (`title`, `image`, `created_at`) VALUES
('The Grand Auditorium',    'gallery1.jpg', '2024-01-05 10:00:00'),
('Classic Film Night',      'gallery2.jpg', '2024-01-12 11:00:00'),
('Behind the Camera',       'gallery3.jpg', '2024-02-08 14:00:00'),
('Red Carpet Event 2024',   'gallery4.jpg', '2024-02-20 16:00:00'),
('Screening Room Sessions', 'gallery5.jpg', '2024-03-10 09:00:00'),
('Directors Roundtable',    'gallery6.jpg', '2024-03-25 13:00:00');

ALTER TABLE `news`
  ADD CONSTRAINT `fk_news_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
