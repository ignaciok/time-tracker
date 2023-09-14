# Time Tracker Application

## Description

This is a simple time tracker application built using Symfony, MySQL, and jQuery for Degusta Box's test requirements. 
The application allows users to start and stop tasks via a web interface as well as through the command line. 
It also provides a summary of time spent on each task and the total time worked for the day.

## Installation

1. Clone the repository:

    ```bash
    git clone https://github.com/ignaciok/time-tracker.git
    ```

2. Navigate to the project directory and install PHP dependencies:

    ```bash
    cd time-tracker
    composer install
    ```

3. Install frontend dependencies:

    ```bash
    npm install
    ```

4. Create a `.env.local` file and configure your database settings:

    ```env
    DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name
    ```

5. Create the database and tables:

    ```bash
    php bin/console doctrine:database:create
    php bin/console doctrine:schema:update --force
    ```

6. Start the Symfony development server:

    ```bash
    php bin/console server:run
    ```

7. Open your browser and go to `http://localhost:8000`.

## Usage

### Web Interface

1. Open your browser and navigate to `http://localhost:8000/`.
2. Type the name of the task and click "Start" to start tracking time.
3. Click "Stop" to stop tracking time for the current task.

### Command Line Interface

You can also interact with the time tracker using the command line.

1. **Start a task**
    ```bash
    php bin/console app:time-tracker start "Task Name"
    ```

2. **End a task**
    ```bash
    php bin/console app:time-tracker end "Task Name"
    ```

3. **List all tasks**
    ```bash
    php bin/console app:time-tracker list
    ```

## Database

The application uses MySQL for data storage. Make sure to update the `.env` file with your database credentials.

## Docker

If you prefer to use Docker, you can build and run the application using Docker Compose.

1. Build the Docker images:

    ```bash
    docker-compose up -d --build
    ```

2. Access the application at `http://localhost:9000`.

To stop and remove all Docker containers, you can run:

```bash
docker-compose down
