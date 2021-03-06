var spec = 
{
    "openapi": "3.0.0",
    "info": {
        "title": "Podips Monitor",
        "version": "1.0.0"
    },
    "paths": {
        "/api": {
            "get": {
                "summary": "Podips Monitor",
                "responses": {
                    "200": {
                        "description": "API Swagger"
                    }
                }
            }
        },
        "/kubernetes/{code}/{message}": {
            "get": {
                "parameters": [
                    {
                        "name": "code",
                        "in": "path",
                        "description": "código de status HTTP",
                        "required": true,
                        "schema": {
                            "type": "string"
                        }
                    },
                    {
                        "name": "message",
                        "in": "path",
                        "description": "mensagem de status",
                        "required": true,
                        "schema": {
                            "type": "string"
                        }
                    }
                ],
                "responses": {
                    "200": {
                        "description": "verifica se o podips está lendo do Kubernetes"
                    }
                }
            }
        },
        "/log/{code}/{message}": {
            "get": {
                "parameters": [
                    {
                        "name": "code",
                        "in": "path",
                        "description": "código de status HTTP",
                        "required": true,
                        "schema": {
                            "type": "string"
                        }
                    },
                    {
                        "name": "message",
                        "in": "path",
                        "description": "mensagem de status",
                        "required": true,
                        "schema": {
                            "type": "string"
                        }
                    }
                ],
                "responses": {
                    "200": {
                        "description": "verifica se o podips está gravando no Fluentd"
                    }
                }
            }
        },
        "/podips/{operation}": {
            "get": {
                "parameters": [
                    {
                        "name": "operation",
                        "in": "path",
                        "description": "operação (write,read,all)",
                        "required": true,
                        "schema": {
                            "type": "string"
                        }
                    }
                ],
                "responses": {
                    "200": {
                        "description": "verifica se o podips está gravando no Fluentd"
                    }
                }
            }
        },
        "/queue/{operation}/{code}/{message}": {
            "get": {
                "parameters": [
                    {
                        "name": "operation",
                        "in": "path",
                        "description": "operação (write,read)",
                        "required": true,
                        "schema": {
                            "type": "string"
                        }
                    },
                    {
                        "name": "code",
                        "in": "path",
                        "description": "código de status HTTP",
                        "required": true,
                        "schema": {
                            "type": "string"
                        }
                    },
                    {
                        "name": "message",
                        "in": "path",
                        "description": "mensagem de status",
                        "required": true,
                        "schema": {
                            "type": "string"
                        }
                    }
                ],
                "responses": {
                    "200": {
                        "description": "verifica se o podips está gravando no Fluentd"
                    }
                }
            }
        }
    }
}