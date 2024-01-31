from fastapi import FastAPI
import os
from fastapi.responses import JSONResponse, FileResponse
import uvicorn

app = FastAPI()


@app.get("/relis/texteditor/apis/list_projects")
def list_projects():
    # Your code to list projects goes here
    response = {}
    projects = os.listdir("/u/relis/public_html/workspace")
    for project in projects:
        response[project.strip()] = {
            "files": [
                i
                for i in os.listdir(
                    f"/u/relis/public_html/workspace/{project.strip()}/src-gen"
                )
                if i.endswith(".php")
            ]
        }
    return JSONResponse(response)


@app.get("/relis/texteditor/apis/get_project_configuration")
def get_project_configuration(project_name: str, file_name: str):
    # Your code to get project configuration goes here
    return FileResponse(
        path=f"/u/relis/public_html/workspace/{project_name}/{file_name}",
        media_type="text/plain",
        filename=file_name,
    )


if __name__ == "__main__":
    uvicorn.run(app, port=8081, host="0.0.0.0", log_level="info")
