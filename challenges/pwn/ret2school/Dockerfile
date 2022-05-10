FROM ubuntu:16.04

RUN apt-get update -y && apt-get install -y \
    lib32z1 \
    socat \
    gcc \
    patchelf \
    libc6-dev-i386 \
    && rm -rf /var/lib/apt/lists/*

RUN useradd -m ctf

WORKDIR /ctf
COPY chall.c .
COPY glibc .

RUN gcc -fno-stack-protector -g1 -no-pie -m32 chall.c -o ret2school
RUN patchelf --set-interpreter ./glibc/lib/ld-2.23.so ret2school
RUN rm chall.c

EXPOSE 12000

ENTRYPOINT ["/bin/sh", "-c", "socat TCP-LISTEN:12000,fork,reuseaddr EXEC:LD_PRELOAD=./libc.so.6 ./format_me"]
