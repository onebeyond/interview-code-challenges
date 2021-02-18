# Mysterious API
To complete this code challenge, you need to be able to run Docker in your local machine. Everything should be clear from this file, however if you have any questions, please refer them to your Hiring Manager.

## Description
You will be given an API, however we won't tell you anything about what problems it solves, how it works or how it behaves. You will need to figure it out and build something cool and original in React around this API.

## Usage
Execute the command `docker run -d --name interview-api -p 3000:3000 chaseadams/interview-api` to run your API locally.

## Bonus points

1. Build a proxy API that consumes this original API Docker container and gracefully handles any upstream intermittent failures.
2. Provide application-level caching for your proxy.
3. Dockerise your proxy.
