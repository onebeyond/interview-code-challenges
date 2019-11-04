# fish-tank

## Simulation of a fish tank

Fish Tank Simulator Introduction
The purpose of this exercise is to design a simulator for a fish tank.
The simulator can be run by a human operator who can populate the fish tank with
various kinds of creatures, add food, and change aspects of the environment
such as temperature. The main focus of this exercise is to model the functions and
objects that are involved, expressing their properties and relationships.

## Description

The simulator has the following properties:

- there is only one fish tank
- the user can add fish, snails and fish food
- living fish and snails both eat fish foodp
- piranha fish may also eat other fish

- clockwork fish do not eat anything
- clockwork fish do not breathe; other fish and snails do

- fish swim at different depths:
- piranha fish and clockwork fish swim at all depths
- sun fish swim at the top
- diver fish swim at the bottom
- the user can vary the temperature of the fish tank
- when the temperature falls below 15 degrees centigrade, piranha fish die and float to the surface
- Events in the fish tank are time based;
- every interval: the user may change the state of the fish tank (adding items, changing the temperature etc.)
- inhabitants that eat do
- inhabitants may be eaten or otherwise die

### Extra points
- REST API to allow external clients to interact with the fish tank
- Dockerising the application 
