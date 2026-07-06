pipeline {
    agent any

    environment {
        APP_DIR = '/var/www'
        CONTAINER = 'ntspi-php'
    }

    stages {
        stage('Pre-deploy') {
            parallel {
                stage('Backup Database') {
                    steps {
                        dir("${APP_DIR}") {
                            sh '''
                                mkdir -p backups
                                docker exec ntspi-db mysqldump -u admin -psecret ntspi_db | gzip > backups/ntspi_db_$(date +%F_%H%M).sql.gz
                                find backups -name "*.sql.gz" -mtime +3 -delete
                            '''
                        }
                    }
                }
                stage('Cleanup Old Files') {
                    steps {
                        dir("${APP_DIR}") {
                            sh 'docker exec ntspi-php find /var/www/storage/app/email_attachments -type f -mtime +3 -delete'
                        }
                    }
                }
            }
        }

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
                    sh 'DEPLOY_PREV_SHA=$(git rev-parse HEAD) && echo $DEPLOY_PREV_SHA > .deploy_prev_sha'
                    sh 'git reset --hard'
                    sh 'git pull origin master'
                }
            }
        }

        stage('Install Dependencies') {
            steps {
                dir("${APP_DIR}") {
                    sh 'docker exec ntspi-php php -d open_basedir=none /usr/local/bin/composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader'
                }
            }
        }

        stage('Database Migrations') {
            steps {
                dir("${APP_DIR}") {
                    sh 'docker exec ntspi-php php artisan migrate --force'
                }
            }
            post {
                failure {
                    dir("${APP_DIR}") {
                        sh '''
                            PREV_SHA=$(cat .deploy_prev_sha 2>/dev/null || echo "")
                            if [ -n "$PREV_SHA" ]; then
                                echo "Rolling back to $PREV_SHA"
                                git reset --hard $PREV_SHA
                                docker exec ntspi-php php artisan migrate:rollback --force || true
                                docker exec ntspi-php sh -c "php -d open_basedir=none /usr/local/bin/composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader"
                                docker exec ntspi-php php artisan cache:clear
                                docker restart ntspi-nginx ntspi-php ntspi-php-queue
                            fi
                        '''
                    }
                }
            }
        }

        stage('Build Frontend') {
            steps {
                dir("${APP_DIR}") {
                    sh 'docker exec ntspi-php sh -c "npm ci --legacy-peer-deps && npm run build"'
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

                        docker exec ntspi-php php artisan optimize
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
                            sleep 1
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
            dir("${APP_DIR}") {
                sh 'rm -f .deploy_prev_sha'
            }
        }
        failure {
            echo "Deploy failed. Check build logs in Jenkins."
            dir("${APP_DIR}") {
                sh 'docker exec ntspi-php php artisan up || true'
                sh 'rm -f .deploy_prev_sha'
            }
        }
    }
}
