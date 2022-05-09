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 //ignore


#API 1

#Login : Method = get

  /wp-json/api/v1/login

{
    email: "ragini@ateamsoftsolutions.com",
    password: "1234567890"
}

------------------------------------------
#API 2

#Sign in : Add user details

#Register : Method = post


 /wp-json/api/v1/signup

{
  "title": "Ms",
  "firstname": "Ragini",
  "lastname": "Ananthan",
  "email": "ragini@ateamsoftsolutions.com",
  "phone": "+911234567890",
  "password": "1234567890",
  "cfpassword": "1234567890",
  "toc_accept": "1",
  "role": "practitioner",
  "sec_key": "secKey",
  "btn_submit": true,
  "domain_flag" : 1
}

----------------------------------------
#API 3


#Sign in : Add clinic details ; Method : post

/wp-json/api/v1/signup/2


{
  "provideruid": "00u6y27tva21PD61z4x7",
  "uid": 3432,
  "listings": [
    {
      "title": "Health Sure",
      "address_1": "Pulinkuzhy House",
      "address_2": "Avinissery P.O",
      "location": "14500",
      "postcode": "680306",
      "telephone_1": "07356044678",
      "num_workers": 5,
      "specialization": [
        "Musculoskeletal Physiotherapy",
        "Sports Injury Physiotherapy"
      ],
      "treatments": [
        "Back Pain",
        "Neck Pain"
      ]
    }
  ],
  "btn_submit": true
}

---------------------------------------

#API 4

#Sign in : Add payment details ; Method = post

 /wp-json/api/v1/signup/3

{
  "provideruid": "00u6y27tva21PD61z4x7",
  "uid": 3432,
  "paymentData": {
    "creditor": "ABC",
    "mandate": "XYZ",
    "customer": "123",
    "customer_bank_account": "1234567890"
  }
}



---------------------------------------------

#API 5

#Adding clinic timings ; method = post

 /wp-json/api/v1/signup/clinic-details


{
  "provideruid": "00u6y27tva21PD61z4x7",
  "uid": 3432,
  "initialAssessment": 10,
  "followUpTreatment":20,
  "openingTimes":[
    {
      "weekDays":[
        {
          "start":"10:00 AM",
          "end":"1:00 PM"
        },{
          "start":"2:00 AM",
          "end":"5:00 PM"
        }
      ],
      "saturday":[
        {
          "start":"10:00 AM",
          "end":"1:00 PM"
        },{
          "start":"2:00 AM",
          "end":"3:00 PM"
        }
      ]
    }
   ],
  "parkingMode":"none",
  "paymentMode":"debit",
  "insurancesAccepted":[
    "Bupa",
    "Aviva"
  ],
  "clinicDescription":"Provides customer friendly enviroment",
  "logo":"2345jhjgsyufd5s67ahdjkakskl"
}


---------------------------------------------------

#API 6

#Adding physiotherapist : method = post

  /wp-json/api/v1/signup/practitioner-details

{
  "provideruid": "00u6y27tva21PD61z4x7",
  "uid": 3432,
  "firstName": "John",
  "lastName": "Smith",
  "profileImage": "5768hgtydufghj43fgjuhgjk",
  "HCPC": "acde1234",
  "CSP": "xyz123456",
  "specialization": [
    "Musculoskeletal Physiotherapy",
    "Sports Injury Physiotherapy"
  ],
  "description": "2+ years of experience in Multispeciality Hospital"
}