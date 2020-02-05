# SocialManager

Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.
Prerequisites

What things you need to install the software and how to install them?

    Composer
    Docker CE
    Docker Compose

Install

    https://github.com/Ylahjaily/SocialManager.git
    docker-compose up -d
    docker-compose exec web composer install
    docker-compose exec web php bin/console doctrine:database:create
    docker-compose exec web php bin/console doctrine:schema:update --force
    