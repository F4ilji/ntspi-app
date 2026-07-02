pipeline {
    agent any

    environment {
        APP_DIR = '/var/www'
        SERVICES = 'app webserver php-queue-worker'
    }

    stages {
        stage('Maintenance Mode') {
            steps {
                dir("${APP_DIR}") {
                    sh 'docker compose exec app php artisan down || true'
                }
            }
        }

        stage('Pull from Git') {
            steps {
                dir("${APP_DIR}") {
                    sh 'git config --global --add safe.directory ${APP_DIR}'
                    sh 'git reset --hard'
                    sh 'git pull origin master'
                }
            }
        }

        stage('Install Dependencies') {
            steps {
                dir("${APP_DIR}") {
                    sh 'docker compose run --rm app composer install --no-dev --no-interaction --prefer-dist --no-cache'
                }
            }
        }

        stage('Database Migrations') {
            steps {
                dir("${APP_DIR}") {
                    sh 'docker compose exec app php artisan migrate --force'
                }
            }
        }

        stage('Build Frontend') {
            steps {
                dir("${APP_DIR}") {
                    sh 'docker compose exec app npm install --legacy-peer-deps'
                    sh 'docker compose exec app npm run build'
                }
            }
        }

        stage('Clear & Warm Caches') {
            steps {
                dir("${APP_DIR}") {
                    sh '''
                        docker compose exec app php artisan cache:clear
                        docker compose exec app php artisan config:clear
                        docker compose exec app php artisan route:clear
                        docker compose exec app php artisan view:clear
                        docker compose exec app php artisan filament:clear-cached-components
                        docker compose exec app php artisan filament:optimize-clear

                        docker compose exec app php artisan route:cache
                        docker compose exec app php artisan view:cache
                        docker compose exec app php artisan icons:cache
                        docker compose exec app php artisan filament:cache-components
                        docker compose exec app php artisan filament:optimize
                    '''
                }
            }
        }

        stage('Fix Permissions') {
            steps {
                dir("${APP_DIR}") {
                    sh 'docker exec -u root ntspi-php chown -R www-data:www-data storage bootstrap/cache'
                    sh 'docker exec -u root ntspi-php chmod -R 775 storage bootstrap/cache'
                }
            }
        }

        stage('Restart Services') {
            steps {
                dir("${APP_DIR}") {
                    sh "docker compose stop ${SERVICES}"
                    sh 'docker compose up -d --remove-orphans'
                }
            }
        }

        stage('Health Check') {
            steps {
                dir("${APP_DIR}") {
                    sh '''
                        for i in $(seq 1 30); do
                            if docker compose exec app php artisan about > /dev/null 2>&1; then
                                echo "App is ready"
                                break
                            fi
                            echo "Waiting for app... ($i/30)"
                            sleep 2
                        done
                    '''
                }
            }
        }

        stage('Enable App') {
            steps {
                dir("${APP_DIR}") {
                    sh 'docker compose exec app php artisan up'
                }
            }
        }
    }

    post {
        success {
            echo "Deploy completed successfully"
        }
        failure {
            echo "Deploy failed. Check build logs in Jenkins."
            dir("${APP_DIR}") {
                sh 'docker compose exec app php artisan up || true'
            }
        }
    }
}
