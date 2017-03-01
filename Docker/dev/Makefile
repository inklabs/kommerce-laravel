default: build

start:
	docker-compose up -d
	docker-compose ps

stop:
	docker-compose stop

status:
	docker-compose ps

restart: stop start

top:
	docker-compose top

stats:
	docker stats --format "table {{.Name}}\t{{.CPUPerc}}\t{{.MemUsage}}\t{{.MemPerc}}\t{{.NetIO}}\t{{.BlockIO}}" --no-stream

stats-stream:
	docker stats --format "table {{.Name}}\t{{.CPUPerc}}\t{{.MemUsage}}\t{{.MemPerc}}\t{{.NetIO}}\t{{.BlockIO}}"

clean: stop
	docker-compose rm

build:
	docker-compose build

pull:
	docker-compose pull

tail:
	docker-compose logs -f

.PHONY: start stop status restart top stats stats-stream clean build pull tail
