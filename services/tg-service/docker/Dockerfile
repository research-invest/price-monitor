FROM golang:alpine

WORKDIR /opt/app

COPY ./backend/ticket-system /opt/app/ticket-system
COPY ./backend/.env /opt/app/.env
COPY ./tls /opt/app/tls
COPY ./frontend-clients-static /opt/app/frontend-clients-static

EXPOSE 3001

CMD ["/opt/app/ticket-system", "-mode", "client", "-dev"]