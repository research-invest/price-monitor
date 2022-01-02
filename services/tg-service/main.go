package main

//GOOS=linux GOARCH=amd64 go build -o ./tg-service -a

import (
	"bytes"
	"encoding/json"
	"fmt"
	tgbotapi "github.com/go-telegram-bot-api/telegram-bot-api"
	"github.com/sirupsen/logrus"
	"io/ioutil"
	"log"
	"net/http"
	"strconv"
	"strings"
)

const ChannelUrl = "/channel/"

type SendMessageChannel struct {
	ChatId int64  `json:"chat_id"`
	Text   string `json:"text"`
}

var ChannelBots = make(map[string]*tgbotapi.BotAPI, 5) // 5!!!!!

func main() {
	readConfig()

	for _, channel := range appConfig.Channels {
		channel := channel

		bot, err := tgbotapi.NewBotAPI(channel.Token)
		if err != nil {
			logrus.Panic(err)
		}

		log.Printf("Authorized on account %s", bot.Self.UserName)

		bot.Debug = false // false
		ChannelBots[channel.UrlCode] = bot

		HttpHandlerFunc(ChannelUrl+channel.UrlCode, http.HandlerFunc(ChannelHandler))

		go func() {
			startListenerChannel(channel)
		}()
	}

	port := appConfig.Port
	host := "localhost"

	fmt.Println("Start service on " + host + ":" + strconv.Itoa(port) + ".")

	err := http.ListenAndServe(host+":"+strconv.Itoa(port), nil)
	if err != nil {
		logrus.Fatalf("can't run service : %v", err)
	}
}

func HttpHandlerFunc(pattern string, h http.Handler) {
	http.HandleFunc(pattern, func(w http.ResponseWriter, r *http.Request) {
		defer func() {
			if err := recover(); err != nil {
				WriteHeader(w, fmt.Sprintf("%v - panic occurred:%v", pattern, err))
			}
		}()

		h.ServeHTTP(w, r)
	})
}

func WriteHeader(w http.ResponseWriter, response interface{}) {
	responseJson, err := json.Marshal(response)
	if err != nil {
		w.WriteHeader(http.StatusInternalServerError)
		responseWrite(w, []byte(fmt.Sprintf("can't marshal json : %v", err)))
		return
	}

	w.WriteHeader(http.StatusOK)
	w.Header().Set("Content-Type", "application/json")
	responseWrite(w, responseJson)
}

func responseWrite(w http.ResponseWriter, data []byte) {
	_, err := w.Write(data)
	if err != nil {
		logrus.Warnf("can't write response : %v", err)
	}
}

func startListenerChannel(channel Channel) {
	bot := ChannelBots[channel.UrlCode]

	u := tgbotapi.NewUpdate(0)
	u.Timeout = 60

	updates, _ := bot.GetUpdatesChan(u)

	for update := range updates {
		if update.Message == nil { // ignore any non-Message updates
			continue
		}

		postBody, _ := json.Marshal(map[string]interface{}{
			"command":      update.Message.Command(),
			"chat_id":      update.Message.Chat.ID,
			"first_name":   update.Message.Chat.FirstName,
			"last_name":    update.Message.Chat.LastName,
			"username":     update.Message.Chat.UserName,
			"text_message": update.Message.Text,
		})

		responseBody := bytes.NewBuffer(postBody)

		resp, err := http.Post(channel.UrlApi, "application/json", responseBody)
		if err != nil {
			logrus.Error("An Error Occurred %v", err)

			err, _ = sendMessageInChannel(channel.UrlCode, update.Message.Chat.ID, "Error #21353")
			if err != nil {
				continue
			}

			continue
		}

		defer resp.Body.Close()

		var textMessage string
		body, err := ioutil.ReadAll(resp.Body)
		if err != nil {
			textMessage = err.Error()
			logrus.Error(err)
			continue
		} else {
			textMessage = string(body)
			log.Printf(textMessage)
		}

		err, _ = sendMessageInChannel(channel.UrlCode, update.Message.Chat.ID, textMessage)
		if err != nil {
			continue
		}
	}
}

func ChannelHandler(w http.ResponseWriter, r *http.Request) {
	code := strings.Replace(r.URL.Path, ChannelUrl, "", 1)

	//bot := ChannelBots[code]
	//defer func() {
	//	bot = nil
	//}()

	//if err != nil {
	//	logrus.Fatalf(err.Error())
	//}

	switch r.Method {
	//case "GET":
	// Just send out the JSON version of 'tom'
	//j, _ := json.Marshal(tom)
	//w.Write(j)
	case "POST":
		d := json.NewDecoder(r.Body)
		sendMessage := &SendMessageChannel{}

		err := d.Decode(sendMessage)
		if err != nil {
			http.Error(w, err.Error(), http.StatusInternalServerError)
			return
		}

		err, _ = sendMessageInChannel(code, sendMessage.ChatId, sendMessage.Text)
		if err != nil {
			return
		}

		w.WriteHeader(http.StatusOK)
		fmt.Fprintf(w, "Success")

		//msg := tgbotapi.NewMessage(sendMessage.ChatId, sendMessage.Text)
		//send, err := bot.Send(msg)
		//if err != nil {
		//	return
		//}
	default:
		w.WriteHeader(http.StatusMethodNotAllowed)
		fmt.Fprintf(w, "I can't do that.")
	}
	//_, _ = w.Write([]byte("success\n"))
}

func sendMessageInChannel(code string, chatId int64, message string) (error, bool) {
	bot := ChannelBots[code]
	msg := tgbotapi.NewMessage(chatId, message)

	if _, err := bot.Send(msg); err != nil {
		logrus.Fatalf(err.Error())
		return err, false
	}

	return nil, true
}
