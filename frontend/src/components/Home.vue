<template>

  <div class="calendars">
    <h1>Calendars</h1>
    <button @click="handleAddCalendar">Add</button>

    <ul>
        <li v-for="calendar in calendars" :key="calendar.description">
            <input type="checkbox" v-model="chackedCalendars" name="calendar">
            <label for="calendar">{{ calendar.description }}</label>
        </li>
    </ul>    



    <!-- <ul v-for="user in users">
      <input type="checkbox" v-bind:key="user" v-model="selectedUsers">
       <label>{{user.name}}</label><br>
    </ul> -->
      
    <ul>
        <li v-for="user in selectedUsers">{{user.name}} - {{user.age}}</li>
    </ul>


  </div>


  <div id="calendar">
    <FullCalendar :options="calendarOptions" />
  </div>
  

<GDialog v-model="createEvent" height="500" max-width="500">
  <div class="modal">
    <h1>Add Event</h1>
    <div class="forms">
      <form @submit="create_event">
      <input type="text" v-model="new_event.title" placeholder="Title" required autofocus>
      <input type="datetime" v-model="new_event.start" placeholder="Start" required>
      <input type="datetime" v-model="new_event.end" placeholder="End" required>

      <div class="adv">
         <div class="container">
          <input type="checkbox" v-model="new_event.allDay" name="allday" >
          <label for="allday">All day</label>
        </div>
        <input type="color" v-model="new_event.color" placeholder="Color" required>
      </div>
        <input type="number" v-model="new_event.calendar_id">
      <br><br><br>
      <input type="submit" value="Create">
    </form>
    </div>
  </div>
</GDialog>

<GDialog v-model="updateEvent" height="500" max-width="500">
  <div class="modal">
    <h1>Edit event</h1>
    <div class="forms">
    <form @submit="update_event">
      <input type="text" v-model="new_event.title" placeholder="Title" required autofocus>
      <input type="datetime" v-model="new_event.start" placeholder="Start" required>
      <input type="datetime" v-model="new_event.end" placeholder="End" required>
      <div class="adv">
         <div class="container">
          <input type="checkbox" v-model="new_event.allDay" name="allday" >
          <label for="allday">All day</label>
        </div>
        <input type="color" v-model="new_event.color" placeholder="Color" required>
      </div>
      <br>
      <input type="submit" value="Save">
    </form>
    <button type="button" @click="deleteEvent(index)">Delete</button>
    </div>

  </div>
</GDialog>

<GDialog v-model="addCalendar" height="300" max-width="500">
  <div class="modal">
    <h1>Add Calendar</h1>
    <form @submit="createCalendar">
      <input type="text" placeholder="Title" v-model="MyCalendar.title" required autofocus>
      <input type="submit" value="Add">
    </form>
  </div>
</GDialog>
</template>

<script>
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import timeGridPlugin from '@fullcalendar/timegrid'

import axios from "axios";
import Cookies from 'js-cookie';

import { GDialog } from 'gitart-vue-dialog'

export default {
  name: 'Home',
components: {
    FullCalendar,
    GDialog
  },
 data() {
    return {
       users:[
                {name:'Tom', age:22},
                {name:'Bob', age:25},
                {name:'Sam', age:28},
                {name:'Alice', age:26}
            ],
        selectedUsers:[],

      MyCalendar: {
        title: ""
      },
      new_event:{
        id: 0,
        start: '',
        end: '',
        color: '#000000',
        allDay: false,
        title: '',
        calendar_id: 0
      },
      chackedCalendars: [],
      createEvent: false,
      updateEvent: false,
      addCalendar: false,
      calendars: [],
      calendarOptions: {
        events: [],
        plugins: [ dayGridPlugin, timeGridPlugin, interactionPlugin, ],
        initialView: 'dayGridMonth',
        headerToolbar: {
          left: 'dayGridMonth,timeGridWeek',
          center: 'title',
          right: 'prev,next'
        },
        weekNumbers: true,
        weekends: true,
        navLinks: true,
        selectable: true,
        dateClick: this.handleDateClick,
        eventClick: this.handleEventClick,
        select: this.handleSelect
      }
    }
  },
  mounted(){
    // console.log(Cookies.get('token'));
    if (Cookies.get('token') === undefined) this.$router.push({path: '/auth'});

    axios.defaults.headers['Authorization'] = `Bearer ${Cookies.get('token')}`;

    axios.get('http://127.0.0.1:8000/api/user_calendars')
     .then((response) => { 
       this.calendars = response.data;
      
       axios.get(`http://127.0.0.1:8000/api/calendar/${this.calendars[0].id}/events`)
            .then((response) => { 
              this.calendarOptions.events = response.data;
            })
      })
  },

  methods: {
        handleSelect(info){
          this.new_event.start = info.startStr; 
          this.new_event.end = info.endStr; 
          this.createEvent = !this.createEvent;
        },
        handleEventClick(info){
          this.new_event.id = info.event.id;
          this.new_event.title = info.event.title;
          this.new_event.start = info.event.startStr; 
          this.new_event.end = info.event.endStr; 
          this.new_event.allDay = info.event.allDay;
          this.updateEvent = !this.updateEvent;
        },
        handleAddCalendar(){
          console.log(this.chackedCalendars);
          this.addCalendar = !this.addCalendar;
        },
        
        create_event(e){
            e.preventDefault();
            axios.post('http://127.0.0.1:8000/api/new_event', 
            {
              'start': this.new_event.start,
              'end': this.new_event.end,
              'backgroundColor': this.new_event.color,
              'calendar_id': this.new_event.calendar_id,
              'title': this.new_event.title,
              'allDay': this.new_event.allDay
            }
            )
            .then((response) => {
                console.log(response);
                 axios.get(`http://127.0.0.1:8000/api/calendar/${this.calendars[0].id}/events`)
                  .then((response) => {this.calendarOptions.events = response.data;})
                this.createEvent = !this.createEvent;
            })
        },

        update_event(e){
          e.preventDefault();
          axios.patch(`http://127.0.0.1:8000/api/event/${this.new_event.id}`, 
            {
              'start': this.new_event.start,
              'end': this.new_event.end,
              'backgroundColor': this.new_event.color,
              'calendar_id': 1,
              'title': this.new_event.title,
              'allDay': this.new_event.allDay
            }
            )
            .then((response) => {
                console.log(response);
                   axios.get(`http://127.0.0.1:8000/api/calendar/${this.calendars[0].id}/events`)
                    .then((response) => {this.calendarOptions.events = response.data;})
                this.updateEvent = !this.updateEvent;
            })
        },

        deleteEvent: function() {
          axios.delete(`http://127.0.0.1:8000/api/destroy_event/${this.new_event.id}`)
            .then((response) => {
                console.log(response);
                 axios.get(`http://127.0.0.1:8000/api/calendar/${this.calendars[0].id}/events`)
                  .then((response) => {this.calendarOptions.events = response.data;})
                  this.selected = this.calendars[0].id;
                this.updateEvent = !this.updateEvent;
            })
        },

        createCalendar(e){
          e.preventDefault();
           axios.post('http://127.0.0.1:8000/api/new_calendar', 
            {
              'description': this.MyCalendar.title,
            }
            )
            .then((response) => {
                console.log(response);
                 axios.get('http://127.0.0.1:8000/api/user_calendars')
                .then((response) => { 
                  this.calendars = response.data;})
                this.addCalendar = !this.addCalendar;            
              }) 
        },

        changeCalendar(){
          axios.get(`http://127.0.0.1:8000/api/calendar/${this.selected}/events`)
            .then((response) => { 
              console.log(response);
              this.calendarOptions.events = response.data;
            })
        },

  }
} 
</script>

<style >

#calendar{
  width: 50%;
  float: right;
  margin: 1em;
  padding: 1em;
  border: 1px solid rgba(0, 0, 0, 0.08);
  border-radius: 1em;
  box-shadow: 4px 4px 10px 0px rgba(34, 60, 80, 0.2);
}

.calendars{
  float: left;
}

.modal{
  font-family: 'Raleway', sans-serif;
  padding: 1em 2em 0;
  /* padding-bottom: 0; */
  display: flex;
  flex-direction: column;
}

.forms{
  padding: 1em;
}

.title{
  font-size: 2em;
}


body {
    background: linear-gradient(to right, #FFFFFF, #FFEFBA);
}

.container {
    max-width: 640px;
    font-size: 13px;
    padding: 20px;
    margin-left: 3.5em;
    margin-top: 0.5em;
}

label{
    padding: 8px 12px;
    background-color: rgba(255, 255, 255, .9);
    border: 2px solid rgba(139, 139, 139, .3);
    color: #adadad;
    border-radius: 25px;
    margin: 3px 0px;
    user-select: none;
    transition: all .2s;
}


label::before {
    font-weight: 900;
    font-size: 12px;
    padding: 2px 6px 2px 2px;
    transition: all .3s ease-out;
}

input[type="checkbox"]:checked + label {
    border: 2px solid #1bdbf8;
    background-color: #12bbd4;
    color: #fff;
}

 input[type="checkbox"] {
  position: absolute;
  opacity: 0;
  width: 5em;
} 

*{
  box-sizing: border-box;
}

input, button{
  font-size: 0.95em;
  font-family: 'Raleway', sans-serif;
  margin: 0 auto 1em;
  width: 95%;
  outline: none;
  padding: 10px;
  border: none;
  box-shadow: 4px 4px 8px 0px rgba(34, 60, 80, 0.2);
  background-color: rgba(255, 255, 255, 0.562);
}

input[type=radio]{
  margin:auto;
  width: auto;
}

input[type="color"] {
  border: none;
  background: #fff;
  width: 4em;
  height: 4em;
  overflow: hidden;
  outline: none;
  border-radius: 25%;
  padding: 5px;
}

input:hover{
  background-color: rgba(255, 255, 255);
  transition: all 1s ease-out;
}

input[type=submit]:hover{
    background-color: rgba(14, 214, 57, 0.856);
}

input:active, input:focus{
  box-shadow: 4px 4px 8px 0px rgba(34, 60, 80, 0.4);
}

button:hover{
  background-color: rgb(255, 24, 74);
  transition: all 1s ease-out;
}

.adv{
  display: flex;
}

li{
  margin: 40px;
}

</style>