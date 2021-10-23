<template>
  <div class="main">

  <div class="calendars">
    <div class="list">
        <h1>Calendars</h1>
        <button id="addCalendar" @click="handleAddCalendar">+</button>
    </div>

    <ul>
        <li class="list" v-for="calendar in calendars" :key="calendar.description">
            <div>
              <input type="checkbox" v-model="chackedCalendars" :value="calendar.id" @change="getCheckedCalendars" name="calendar" checked>
              <label for="calendar">{{ calendar.description }}<span>_{{ calendar.id }}</span> </label>
            </div>
            <button class="setting" @click="getCalendarId(calendar.id)"><img src="https://www.pinpng.com/pngs/m/145-1454324_png-file-svg-setting-icon-black-and-white.png" alt=""></button>
        </li>
    </ul>    
  </div>


  <div id="calendar">
    <FullCalendar :options="calendarOptions" />
  </div>
  </div>
  

<GDialog v-model="createEvent" height="600" max-width="500">
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
        <input type="number" v-model="new_event.calendar_id" placeholder="Calendar_id" required>
      <br><br><br>
      <input type="submit" value="Create">
    </form>
    </div>
  </div>
</GDialog>

<GDialog v-model="updateEvent" height="600" max-width="500">
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
      <input type="number" v-model="new_event.calendar_id" placeholder="Calendar_Id" required>
      <br><br><br>
      <input type="submit" value="Save">
    </form>
    <button type="button" @click="deleteEvent(index)">Delete</button>
    </div>

  </div>
</GDialog>

<GDialog v-model="addCalendar" height="250" max-width="500">
  <div class="modal">
    <h1>Add Calendar</h1>
    <form @submit="createCalendar">
      <input type="text" placeholder="Title" v-model="MyCalendar.title" required autofocus>
      <input type="submit" value="Add">
    </form>
  </div>
</GDialog>

<GDialog v-model="setCalendar" height="400" max-width="500">
  <div class="modal">
    <h1>Setting</h1>
    <h3>Share calendar</h3>
    <div class="forms">

      <form @submit="shareCalendarfunc">
        <input type="email" placeholder="Enter e-mail to share" v-model="shareCalendar.email" required autofocus>
        <!-- <input type="text" placeholder="Rule" v-model="shareCalendar.access" required> -->
        <input type="submit" value="Share">
      </form>
      <br>
      <button type="button" @click="deleteCalendar(index)">Delete</button>
    </div>

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
      shareCalendar: {
        email: '',
        calendar_id: undefined,
        access: 'write'
      },
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
        calendar_id: undefined
      },
      chackedCalendars: [],
      createEvent: false,
      updateEvent: false,
      addCalendar: false,
      setCalendar: false,
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
    if (Cookies.get('token') === undefined) this.$router.push({path: '/auth'});

    axios.defaults.headers['Authorization'] = `Bearer ${Cookies.get('token')}`;

    axios.get('http://127.0.0.1:8000/api/user_calendars')
     .then((response) => { 
       this.calendars = response.data;
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
            })
            .then((response) => {
                console.log(response);
                this.getCheckedCalendars();
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
              'calendar_id': this.new_event.calendar_id,
              'title': this.new_event.title,
              'allDay': this.new_event.allDay
            }
            )
            .then((response) => {
                console.log(response);
                this.getCheckedCalendars();
                this.updateEvent = !this.updateEvent;
            })
        },

        deleteEvent: function() {
          axios.delete(`http://127.0.0.1:8000/api/destroy_event/${this.new_event.id}`)
            .then(() => {
                this.getCheckedCalendars();
                this.updateEvent = !this.updateEvent;
            })
        },

        createCalendar(e){
          e.preventDefault();
           axios.post('http://127.0.0.1:8000/api/new_calendar', 
            {
              'description': this.MyCalendar.title,
            })
            .then((response) => {
                console.log(response);
                 axios.get('http://127.0.0.1:8000/api/user_calendars')
                .then((response) => { 
                  this.calendars = response.data;})
                this.addCalendar = !this.addCalendar;            
              }) 
        },

        getCheckedCalendars() {
          this.calendarOptions.events = [];
          this.chackedCalendars.forEach(id => {
            axios.get(`http://127.0.0.1:8000/api/calendar/${id}/events`)
            .then((response) => { 
              console.log(response);
              this.calendarOptions.events.push(...response.data);
            })
          });
        },

        shareCalendarfunc(e){
          e.preventDefault();
          axios.post('http://127.0.0.1:8000/api/share_calendar',
          {
            'email': this.shareCalendar.email,
            'calendar_id': this.shareCalendar.calendar_id,
            'access': 'write'
          })
          .then(() => { 
                  this.setCalendar = !this.setCalendar;
          })
        },

        deleteCalendar(){
          console.log(this.shareCalendar.calendar_id);
          axios.delete(`http://127.0.0.1:8000/api/destroy_calendar/${this.shareCalendar.calendar_id}`)
            .then(() => {
                  axios.get('http://127.0.0.1:8000/api/user_calendars')
                  .then((response) => { this.calendars = response.data;})
                this.getCheckedCalendars();
                this.setCalendar = !this.setCalendar;
            })
        },

        getCalendarId(id) {
          this.shareCalendar.calendar_id = id;
          this.setCalendar = !this.setCalendar;
        }


  }
} 
</script>

<style scoped>
  @import "./style.css";
</style>
