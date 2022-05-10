FROM ubuntu:16.04

RUN apt-get update -y && apt-get install -y \
    lib32z1 \
    socat \
    gcc \
    && rm -rf /var/lib/apt/lists/*

RUN useradd -m ctf

WORKDIR /ctf
COPY chall.c .

RUN gcc -g1 chall.c -o format_me
RUN rm chall.c

EXPOSE 12000

ENTRYPOINT ["/bin/sh", "-c", "socat TCP-LISTEN:12000,fork,reuseaddr EXEC:./format_me"]
