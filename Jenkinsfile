pipeline {

  environment {
    registryCredential = 'dockerhub'
    imagefolder = "masterarbeithhz/usecasepaxcounter:"
    imagetag = "processing${env.BUILD_ID}"
    giturl = 'https://github.com/masterarbeithhz/UseCaseArchitecture_Processing.git'
    PROJECT_ID = 'crafty-sound-297315'
    CLUSTER_NAME = 'cluster-6'
    LOCATION = 'us-central1-c'
    CREDENTIALS_ID = 'crafty-sound-297315'
  }
  
  agent any

  stages {

    stage('Checkout groovy') {
      steps {
        git url:"https://github.com/masterarbeithhz/CustomerConfiguration.git", branch:'main'
        
      }
    }

    stage("Load config") {
      steps {
        script {
          load "${customer_groovy}.groovy"
          echo "${env.UC_CUSTOMER}"
          echo "${env.UC_DBNAME}"
          echo "${env.UC_DBUSER}"
          echo "${env.UC_DBDB}"
          echo "${env.UC_DBPSWD}"
          echo "${env.UC_DOMAIN}"
        }
      }
    }

    stage('Checkout Source') {
      steps {
        git url:"${giturl}", branch:'main'
      }
    }
    
      stage("Build image") {
            steps {
                script {
                    myapp = docker.build("${imagefolder}${imagetag}")
                }
            }
        }
    
      stage("Push image") {
            steps {
                script {
                    docker.withRegistry('https://registry.hub.docker.com', registryCredential) {
                            myapp.push("${imagetag}")
                    }
                }
            }
        }

       stage("Prepare Yaml") {
        steps {
          script {
            def data = readFile file: "kubmanifest.yaml"
            data = data.replaceAll("JSVAR_DOCKERIMAGE", "${imagefolder}${imagetag}")
            data = data.replaceAll("JSVAR_UC_CUSTOMER", "${env.UC_CUSTOMER}")
            data = data.replaceAll("JSVAR_UC_DBNAME", "${env.UC_DBNAME}")
            data = data.replaceAll("JSVAR_UC_DBUSER", "${env.UC_DBUSER}")
            data = data.replaceAll("JSVAR_UC_DBDB", "${env.UC_DBDB}")
            data = data.replaceAll("JSVAR_UC_DBPSWD", "${env.UC_DBPSWD}")
            data = data.replaceAll("JSVAR_UC_DOMAIN", "${env.UC_DOMAIN}")
            data = data.replaceAll("JSVAR_NAMESPACE", "${env.C_NAMESPACE}")
            echo data
            writeFile file: "kubmanifest.yaml", text: data
          }
        }
      }
    
    
        stage('Deploy to GKE') {
            steps{
                step([$class: 'KubernetesEngineBuilder', projectId: env.PROJECT_ID, namespace:env.C_NAMESPACE, clusterName: env.CLUSTER_NAME, location: env.LOCATION, manifestPattern: 'kubmanifest.yaml', credentialsId: env.CREDENTIALS_ID, verifyDeployments: true])
            }
        }

  }

}
