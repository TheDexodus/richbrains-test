# Richbrains Test

## Install

### 1. Create environment file

```shell
cp .env.dist .env
```

### 2. Up project

```shell
docker-compose up -d
```

### 3. Run build

```shell
docker-compose exec php phing -f build.xml
```