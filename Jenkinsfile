pipeline {
    agent any

    environment {
        APP_DIR = '/var/www'
        CONTAINER = 'ntspi-php'
    }

    stages {
        stage('Maintenance Mode') {
            steps {
                dir("${APP_DIR}") {
                    sh 'docker exec ntspi-php php artisan down || true'
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
                    sh 'docker exec ntspi-php composer install --no-dev --no-interaction --prefer-dist --no-cache'
                }
            }
        }

        stage('Database Migrations') {
            steps {
                dir("${APP_DIR}") {
                    sh 'docker exec ntspi-php php artisan migrate --force'
                }
            }
        }

        stage('Build Frontend') {
            steps {
                dir("${APP_DIR}") {
                    sh 'docker exec ntspi-php npm install --legacy-peer-deps'
                    sh 'docker exec ntspi-php npm run build'
                }
            }
        }

        stage('Clear & Warm Caches') {
            steps {
                dir("${APP_DIR}") {
                    sh '''
                        docker exec ntspi-php php artisan cache:clear
                        docker exec ntspi-php php artisan config:clear
                        docker exec ntspi-php php artisan route:clear
                        docker exec ntspi-php php artisan view:clear
                        docker exec ntspi-php php artisan filament:clear-cached-components
                        docker exec ntspi-php php artisan filament:optimize-clear

                        docker exec ntspi-php php artisan route:cache
                        docker exec ntspi-php php artisan view:cache
                        docker exec ntspi-php php artisan icons:cache
                        docker exec ntspi-php php artisan filament:cache-components
                        docker exec ntspi-php php artisan filament:optimize
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
                    sh 'docker exec -u root ntspi-php chown -R www-data:www-data storage bootstrap/cache'
                    sh 'docker restart ntspi-nginx ntspi-php ntspi-php-queue'
                }
            }
        }

        stage('Health Check') {
            steps {
                dir("${APP_DIR}") {
                    sh '''
                        for i in $(seq 1 30); do
                            if docker exec ntspi-php php artisan about > /dev/null 2>&1; then
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
                    sh 'docker exec ntspi-php php artisan up'
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
                sh 'docker exec ntspi-php php artisan up || true'
            }
        }
    }
}
