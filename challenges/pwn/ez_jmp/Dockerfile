FROM ubuntu:16.04

RUN apt-get update -y && apt-get install -y \
    lib32z1 \
    socat \
    libc6-dev-i386 \
    gcc \
    && rm -rf /var/lib/apt/lists/*

RUN useradd -m ctf

WORKDIR /ctf
COPY flag.txt .
COPY chall.c .

RUN gcc -fno-stack-protector -g1 -no-pie -m32 -z execstack chall.c -o ez_jmp
RUN rm chall.c

EXPOSE 12000

ENTRYPOINT ["/bin/sh", "-c", "socat TCP-LISTEN:12000,fork,reuseaddr EXEC:./ez_jmp"]
