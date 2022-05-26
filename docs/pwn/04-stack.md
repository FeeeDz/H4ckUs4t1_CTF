Overview
========

What is Buffer Overflow? Why it is a problem?
---------------------------------------------

Shortly the buffer overflow is an anomaly, where a program, while
writing data to a buffer, overruns the buffer\'s boundary. Namely
buffers are created by fixed size so if we pass more data than the
buffer can store, buffer will overflow. When a buffer overflowed, the
program can access other parts of memory which belong to other programs.
As you think, this may cause really big problems.

Normally we shouldn\'t have permissions to access other parts of memory
but in some cases it may be. Then we would have the vulnerable program
and nothing is safe with that program. Here the Buffer Overflow is!

Where can we face with Buffer Overflow?
---------------------------------------

We can face with the Buffer Overflow vulnerability in C/C++ technologies
because those technologies have no built-in protection against accessing
or overwriting data in any part of their memory about buffer limits and
includes some vulnerable functions. But buffer overflows also can exist
in any programming environment where direct memory manipulation is
allowed.

Along with that, if a programmer who programmes with those technologies,
uses the vulnerable functions without controlling limits of buffer,
there is going to be a big problem for security. As you understand it
from the project title, we are going to examine the gets function that
is the one of vulnerable functions of C, in this project.

What is the size of the danger of Buffer Overflow?
--------------------------------------------------

It completely depends on the imagination of the attacker. In fact, the
attacker can do anything which wants, such as deleting files, stealing
information, using the computer in other attacks.

Important points to better understand Buffer Overflow
-----------------------------------------------------

We should know how memory runs for better understanding the Buffer
Overflow. That's why we will dive deeper into memory and some registers.

By the way we are trying to explore buffer overflow in Stack but it may
be also in Heap. We will just give some short informations about other
parts of memory and then mostly talk about Stack. We can see whole
memory structure in next page...

**MEMORY FOR A RUNNING PROCESS**

![](http://i.stack.imgur.com/1Yz9K.gif]{.underline}) Source:
[*[http://i.stack.imgur.com/1Yz9K.gif]{.underline}*](http://i.stack.imgur.com/1Yz9K.gif)

1.  **Stack**: This is the place where all the function parameters,
    return addresses and the local variables of the function are stored.
    It's a LIFO structure. It grows downward in memory(from higher
    address space to lower address space) as new function calls are
    made. We will examine the stack in more detail later.

2.  **Heap**: All the dynamically allocated memory resides here.
    Whenever we use malloc to get memory dynamically, it is allocated
    from the heap. The heap grows upwards in memory(from lower to higher
    memory addresses) as more and more memory is required.

3.  **Command line arguments and environment variables**: The arguments
    passed to a program before running and the environment variables are
    stored in this section.

4.  **Uninitialized data(Bss Segment)**: All the uninitialized data is
    stored here. This consists of all global and static variables which
    are not initialized by the programmer. The kernel initializes them
    to arithmetic 0 by default.

5.  **Initialized data(Data Segment)**: All the initialized data is
    stored here. This constists of all global and static variables which
    are initialised by the programmer.

6.  **Text**: This is the section where the executable code is stored.
    The loader loads instructions from here and executes them. It is
    often read only.

**SOME IMPORTANT REGISTERS**

1.  **%rip**: The **Instruction pointer register**. It stores the
    address of the next instruction to be executed. After every
    instruction execution it's value is incremented depending upon the
    size of an instrution.

2.  **%rsp**: The **Stack pointer register**. It stores the address of
    the top of the stack. This is the address of the last element on the
    stack. The stack grows downward in memory(from higher address values
    to lower address values). So the %rsp points to the value in stack
    at the lowest memory address.

3.  **%rbp**: The **Base pointer register**. The %rbp register usually
    set to %rsp at the start of the function. This is done to keep tab
    of function parameters and local variables. Local variables are
    accessed by subtracting offsets from %rbp and function parameters
    are accessed by adding offsets to it as you shall see in the next
    section.

**MEMORY MANAGEMENT DURING FUNCTION CALLS**

![](https://raw.githubusercontent.com/muhammet-mucahit/Security-Exercises/master/Screenshots/2.png)

Assume our **%rip** is pointing to the **func** call in **main**. The
following steps would be taken:

1.  A function call is found, push parameters on the stack from right to
    left(in reverse order). So **2** will be pushed first and
    then **1**.

2.  We need to know where to return after **func** is completed, so push
    the address of the next instruction on the stack.

3.  Find the address of **func** and set **%rip** to that value. The
    control has been transferred to **func()**.

4.  As we are in a new function we need to update **%rbp**. Before
    updating we save it on the stack so that we can return later back
    to **main**. So **%rbp** is pushed on the stack.

5.  Set **%rbp** to be equal to **%rsp**. **%rbp** now points to current
    stack pointer.

6.  Push local variables onto the stack/reserver space for them on
    stack. **%rsp** will be changed in this step.

7.  After **func** gets over we need to reset the previous stack frame.
    So set **%rsp** back to **%rbp**. Then pop the earlier **%rbp** from
    stack, store it back in **%rbp**. So the base pointer register
    points back to where it pointed in **main**.

8.  Pop the return address from stack and set **%rip** to it. The
    control flow comes back to **main**, just after
    the **func** function call.

This is how the stack would look while in **func**.

![](https://raw.githubusercontent.com/muhammet-mucahit/Security-Exercises/master/Screenshots/3.png)

Sources
=======

[[https://www.youtube.com/watch?v=1S0aBV-Waeo]{.underline}](https://www.youtube.com/watch?v=1S0aBV-Waeo)

[[http://www.therabb1thole.co.uk/tutorial/linux-64-bit-buffer-overflow-tutorial/]{.underline}](http://www.therabb1thole.co.uk/tutorial/linux-64-bit-buffer-overflow-tutorial/)

[[https://dl.packetstormsecurity.net/papers/attack/64bit-overflow.pdf]{.underline}](https://dl.packetstormsecurity.net/papers/attack/64bit-overflow.pdf)

[[https://stackoverflow.com/questions/15533889/buffer-overflows-on-64-bit]{.underline}](https://stackoverflow.com/questions/15533889/buffer-overflows-on-64-bit)

[[https://dhavalkapil.com/blogs/Buffer-Overflow-Exploit/]{.underline}](https://dhavalkapil.com/blogs/Buffer-Overflow-Exploit/)

[[https://bytesoverbombs.io/exploiting-a-64-bit-buffer-overflow-469e8b500f10]{.underline}](https://bytesoverbombs.io/exploiting-a-64-bit-buffer-overflow-469e8b500f10)

[[https://samsclass.info/127/proj/p13-64bo.htm]{.underline}](https://samsclass.info/127/proj/p13-64bo.htm)

[[https://blog.techorganic.com/2015/04/10/64-bit-linux-stack-smashing-tutorial-part-1/]{.underline}](https://blog.techorganic.com/2015/04/10/64-bit-linux-stack-smashing-tutorial-part-1/)
